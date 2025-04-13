<?php

namespace App\Services;

use App\Models\PaymentLog;

class PaymentLogService
{
    public function logPaymentAttempt($data, $response = null)
    {
        return PaymentLog::create([
            'transaction_id' => $response->getTransactionID() ?? null,
            'customer_id' => $data['customerProfileId'] ?? null,
            'amount' => $data['amount'] ?? 0.00,
            'status' => $response ? $response->getResponseCode() === '1' ? 'success' : 'failed' : 'pending',
            'response_code' => $response ? $response->getResponseCode() : null,
            'response_message' => $response ? $response->getMessages()[0]->getText() : null,
            'request_data' => json_encode($data),
            'response_data' => $response ? json_encode($response->toJson()) : null,
        ]);
    }
}
