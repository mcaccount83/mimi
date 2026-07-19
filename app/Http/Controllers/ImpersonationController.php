<?php

namespace App\Http\Controllers;

use App\Services\ImpersonationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    public function __construct(private ImpersonationService $impersonation) {}

    public function start(int $userId, Request $request): RedirectResponse
    {
        $this->impersonation->start($userId, $request->query('returnTo'));
        return redirect()->route('home');
    }

    public function stop(): RedirectResponse
    {
        $returnRoute = $this->impersonation->getReturnRoute();

        $this->impersonation->stop();

        if ($returnRoute) {
            return redirect()->route($returnRoute['route'], $returnRoute['params']);
        }

        return redirect()->route('home');
    }
}
