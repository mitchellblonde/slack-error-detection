<?php

declare(strict_types=1);

namespace SlackErrorNotifier;

use DateTimeImmutable;
use Throwable;

final class ErrorReporter
{
    public function __construct(private readonly SlackWebhookClient $webhookClient)
    {
    }

    public function report(Throwable $throwable): bool
    {
        return $this->webhookClient->sendMessage($this->formatErrorMessage($throwable));
    }

    public function formatErrorMessage(Throwable $throwable, ?string $host = null, ?DateTimeImmutable $date = null): string
    {
        $hostValue = $host ?? gethostname() ?: 'unknown-host';
        $dateValue = ($date ?? new DateTimeImmutable())->format(DateTimeImmutable::ATOM);

        return sprintf(
            "Host: %s\nDate: %s\nError: %s",
            $hostValue,
            $dateValue,
            $throwable->getMessage(),
        );
    }
}
