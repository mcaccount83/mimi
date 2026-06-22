<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LearnDashService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    const TIMEOUT = 10;
    const COURSES_CACHE_TTL = 1800;  // 30 min
    const PROGRESS_CACHE_TTL = 300;  // 5 min

    public function __construct()
    {
        $this->baseUrl  = rtrim(config('services.learndash.base_url'), '/');
        $this->username = config('services.learndash.user');
        $this->password = config('services.learndash.password');
    }

    public function getAutoLoginUrl($course, $laravelUser, $routeName = 'course.redirect')
    {
        $payload = [
            'user_id'    => $laravelUser->id,
            'email'      => $laravelUser->email,
            'first_name' => $laravelUser->first_name,
            'last_name'  => $laravelUser->last_name,
            'user_type'  => $laravelUser->user_type,
            'expires'    => time() + 300,
        ];

        $token = base64_encode(json_encode($payload));

        return route($routeName, ['course_id' => $course['id']])
            . '?token=' . urlencode($token)
            . '&course_url=' . urlencode($course['link']);
    }

    public function getCoursesForUserType(string $userTypeId): array
    {
        $cacheKey = "learndash_courses_{$userTypeId}";

        return Cache::remember($cacheKey, self::COURSES_CACHE_TTL, function () use ($userTypeId) {
            try {
                $response = Http::timeout(self::TIMEOUT)
                    ->withHeaders([
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma'        => 'no-cache',
                    ])
                    ->get("{$this->baseUrl}/wp-json/public/v1/courses/group/{$userTypeId}");

                if ($response->successful()) {
                    return $response->json() ?? [];
                }

                Log::warning('LearnDash getCoursesForUserType non-success', [
                    'userTypeId' => $userTypeId,
                    'status'     => $response->status(),
                ]);

                return [];

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('LearnDash getCoursesForUserType timeout/connection error', [
                    'userTypeId' => $userTypeId,
                    'error'      => $e->getMessage(),
                ]);
                return null;
            }
        }) ?? [];
    }

    public function getUserProgress(string $email): array
    {
        $cacheKey = 'learndash_progress_' . md5($email);

        return Cache::remember($cacheKey, self::PROGRESS_CACHE_TTL, function () use ($email) {
            try {
                $response = Http::timeout(self::TIMEOUT)
                    ->withHeaders(['Cache-Control' => 'no-cache, no-store, must-revalidate'])
                    ->get("{$this->baseUrl}/wp-json/public/v1/user-progress", [
                        'email' => $email,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return collect($data['courses'] ?? [])
                        ->keyBy('course_id')
                        ->toArray();
                }

                Log::warning('LearnDash getUserProgress non-success', [
                    'email'  => $email,
                    'status' => $response->status(),
                ]);

                return [];

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('LearnDash getUserProgress timeout/connection error', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        }) ?? [];
    }

    public function getBulkUserProgress(array $emails): array
    {
        if (empty($emails)) {
            return [];
        }

        $results  = [];
        $uncached = [];

        foreach ($emails as $email) {
            $cacheKey = 'learndash_bulk_progress_' . md5($email);
            $cached   = Cache::get($cacheKey);

            if ($cached !== null) {
                $results[$email] = $cached;
            } else {
                $uncached[] = $email;
            }
        }

        if (!empty($uncached)) {
            try {
                $response = Http::timeout(self::TIMEOUT)
                    ->withHeaders(['Cache-Control' => 'no-cache, no-store, must-revalidate'])
                    ->get("{$this->baseUrl}/wp-json/public/v1/users-progress", [
                        'emails' => $uncached,
                    ]);

                if ($response->successful()) {
                    $data = $response->json() ?? [];

                    foreach ($data as $email => $progress) {
                        $cacheKey        = 'learndash_bulk_progress_' . md5($email);
                        Cache::put($cacheKey, $progress, self::PROGRESS_CACHE_TTL);
                        $results[$email] = $progress;
                    }
                } else {
                    Log::warning('LearnDash getBulkUserProgress non-success', [
                        'status'         => $response->status(),
                        'uncached_count' => count($uncached),
                    ]);
                }

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('LearnDash getBulkUserProgress timeout/connection error', [
                    'error'          => $e->getMessage(),
                    'uncached_count' => count($uncached),
                ]);
            }
        }

        return $results;
    }

    public function bustCoursesCache(string $userTypeId): void
    {
        Cache::forget("learndash_courses_{$userTypeId}");
    }
}
