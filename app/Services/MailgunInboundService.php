<?php

namespace App\Services\Mail;

use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MailgunInboundService
{
    public function verifySignature(Request $request): bool
    {
        $token = $request->input('token');
        $timestamp = $request->input('timestamp');
        $signature = $request->input('signature');
        $signingKey = config('services.mailgun_inbound.signing_key');

        if (! $token || ! $timestamp || ! $signature || ! $signingKey) {
            return false;
        }

        // Reject stale requests (replay protection) — 15 min window
        if (abs(time() - (int) $timestamp) > 900) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp . $token, $signingKey);

        return hash_equals($expected, $signature);
    }

    public function handleInbound(Request $request): void
    {
        $senderEmail = $request->input('sender');
        $recipient = $request->input('recipient');
        $subject = $request->input('subject', '(no subject)');
        $body = trim($request->input('stripped-text', ''));

        $user = \App\Models\User::where('email', $senderEmail)->first();

        if (! $user) {
            Log::info("Inbound mail from unrecognized sender: {$senderEmail}");
            return;
        }

        $inReplyTo = $this->extractHeader($request, 'In-Reply-To')
            ?? $this->extractHeader($request, 'References');

        $thread = $inReplyTo
            ? Thread::where('message_id', $this->normalizeMessageId($inReplyTo))->first()
            : null;

        Auth::login($user);

        if ($thread) {
            $post = $thread->posts()->create([
                'user_id' => $user->id,
                'content' => $body,
                'message_id' => $this->extractHeader($request, 'Message-Id'),
            ]);
        } else {
            // No thread match — route by recipient local-part to a category,
            // fall back to a default catch-all category.
            $localPart = explode('@', $recipient)[0];
            $category = Category::whereRaw('LOWER(title) = ?', [strtolower($localPart)])->first();

            if (! $category) {
                Log::warning("Inbound mail with no matching category for recipient: {$recipient}");
                Auth::logout();
                return;
            }

            $thread = $category->threads()->create([
                'user_id' => $user->id,
                'title' => $subject,
                'message_id' => $this->extractHeader($request, 'Message-Id'),
            ]);

            $thread->posts()->create([
                'user_id' => $user->id,
                'content' => $body,
                'message_id' => $this->extractHeader($request, 'Message-Id'),
            ]);
        }

        Auth::logout();
    }

    private function extractHeader(Request $request, string $name): ?string
    {
        $headers = json_decode($request->input('message-headers', '[]'), true);

        foreach ($headers as [$key, $value]) {
            if (strcasecmp($key, $name) === 0) {
                return $this->normalizeMessageId($value);
            }
        }

        return null;
    }

    private function normalizeMessageId(string $value): string
    {
        // References can contain multiple ids space-separated; take the last (most recent)
        $parts = preg_split('/\s+/', trim($value));
        return trim(end($parts), '<>');
    }
}
