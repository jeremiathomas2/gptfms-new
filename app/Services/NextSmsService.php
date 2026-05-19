<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NextSmsService
{
    protected $baseUrl = 'https://messaging-service.co.tz/api/v1';
    protected $username;
    protected $password;
    protected $senderId;

    public function __construct()
    {
        $this->username = env('NEXTSMS_USERNAME');
        $this->password = env('NEXTSMS_PASSWORD');
        $this->senderId = env('NEXTSMS_SENDER_ID', 'NEXTSMS');
    }

    public function sendSms($to, $message)
    {
        // Format number to international format if needed
        $to = $this->formatNumber($to);

        if (env('APP_ENV') === 'local' || env('NEXTSMS_MOCK', true)) {
            Log::info("MOCK SMS to {$to}: {$message}");
            return true;
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->post("{$this->baseUrl}/send-sms", [
                    'from' => $this->senderId,
                    'to' => $to,
                    'text' => $message
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("NextSMS Error: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("NextSMS Exception: " . $e->getMessage());
            return false;
        }
    }

    protected function formatNumber($number)
    {
        // Basic TZ number formatting (0... -> 255...)
        if (str_starts_with($number, '0')) {
            return '255' . substr($number, 1);
        }
        return $number;
    }
}
