<?php

declare(strict_types=1);

namespace SlackErrorNotifier;

final class SlackWebhookClient
{
    public function __construct(
        private readonly string $webhookUrl,
        private readonly float $timeoutSeconds = 2.5,
    ) {
    }

    public function sendMessage(string $message): bool
    {
        $payload = json_encode([
            'text' => $message,
        ], JSON_THROW_ON_ERROR);

        $ch = curl_init($this->webhookUrl);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-type: application/json'],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
        ]);

        curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }
}
