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
        $websiteValue = $this->resolveWebsite();
        $userValue = $this->resolveUser();
        $dateValue = ($date ?? new DateTimeImmutable())->format(DateTimeImmutable::ATOM);

        return sprintf(
            "Host: %s\nWebsite: %s\nUser: %s\nDate: %s\nFile: %s\nLine: %d\nError: %s",
            $hostValue,
            $websiteValue,
            $userValue,
            $dateValue,
            $throwable->getFile(),
            $throwable->getLine(),
            $throwable->getMessage(),
        );
    }

    private function resolveWebsite(): string
    {
        $appUrl = getenv('APP_URL');
        if (is_string($appUrl) && $appUrl !== '') {
            return $appUrl;
        }

        $httpHost = $_SERVER['HTTP_HOST'] ?? null;
        if (is_string($httpHost) && $httpHost !== '') {
            $scheme = ($_SERVER['REQUEST_SCHEME'] ?? 'https');

            return sprintf('%s://%s', $scheme, $httpHost);
        }

        return 'unknown-website';
    }

    private function resolveUser(): string
    {
        if (function_exists('auth')) {
            $authUser = auth()->user();
            if ($authUser !== null) {
                $identifier = method_exists($authUser, 'getAuthIdentifier')
                    ? (string) $authUser->getAuthIdentifier()
                    : (string) ($authUser->id ?? 'authenticated-user');

                return $identifier !== '' ? $identifier : 'authenticated-user';
            }
        }

        $sessionUser = $_SESSION['user'] ?? null;
        if (is_string($sessionUser) && $sessionUser !== '') {
            return $sessionUser;
        }

        return 'guest';
    }
}
