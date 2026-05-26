<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NextSmsService
{
    protected $senderId;
    protected $apiToken;
    protected $baseUrl;
    protected bool $testMode;

    public function __construct()
    {
        $this->senderId = env('NEXTSMS_SENDER_ID', 'NEXTSMS');
        $this->apiToken = env('NEXTSMS_API_TOKEN');
        $this->baseUrl = rtrim(env('NEXTSMS_BASE_URL', 'https://messaging-service.co.tz'), '/');
        $this->testMode = filter_var(env('NEXTSMS_TEST_MODE', false), FILTER_VALIDATE_BOOL);
    }

    public function sendSms($to, $message)
    {
        $to = $this->formatNumber($to);

        if (env('NEXTSMS_MOCK', false)) {
            Log::info("MOCK SMS to {$to}: {$message}");
            return true;
        }

        try {
            if (!$this->apiToken) {
                Log::error('NextSMS Error: NEXTSMS_API_TOKEN is not configured.');
                return false;
            }

            $endpoint = $this->testMode ? '/api/sms/v2/test/text/single' : '/api/sms/v2/text/single';

            $response = Http::withToken($this->apiToken)
                ->acceptJson()
                ->asJson()
                ->timeout(30)
                ->post("{$this->baseUrl}{$endpoint}", [
                    'from' => $this->senderId,
                    'to' => $to,
                    'text' => $message,
                ]);

            if ($response->successful()) {
                $json = $response->json();
                if (is_array($json)) {
                    $messageId = $json['messages'][0]['messageId'] ?? null;
                    $status = $json['messages'][0]['status'] ?? null;
                    Log::info('NextSMS Sent', [
                        'to' => $to,
                        'messageId' => $messageId,
                        'status' => $status,
                    ]);
                } else {
                    Log::info('NextSMS Sent', ['to' => $to]);
                }
                return true;
            }

            $body = $response->body();
            if (is_string($body) && strlen($body) > 1200) {
                $body = substr($body, 0, 1200) . '...';
            }
            Log::error("NextSMS Error: HTTP {$response->status()} - {$body}");
            return false;
        } catch (\Throwable $e) {
            Log::error("NextSMS Exception: " . $e->getMessage());
            return false;
        }
    }

    protected function formatNumber($number)
    {
        $raw = trim((string) $number);
        if ($raw === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $raw) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '255') && strlen($digits) === 12) {
            return $digits;
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return '255' . substr($digits, 1);
        }

        if (strlen($digits) === 9) {
            return '255' . $digits;
        }

        return $digits;
    }
}
