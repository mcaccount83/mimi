<?php

namespace App\Http\Controllers;

use App\Services\Mail\MailgunInboundService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InboundMailController extends Controller
{
    public function __construct(private MailgunInboundService $mailgunInbound) {}

    public function handle(Request $request): Response
    {
        if (! $this->mailgunInbound->verifySignature($request)) {
            return response('Invalid signature', 403);
        }

        $this->mailgunInbound->handleInbound($request);

        return response('OK', 200);
    }
}
