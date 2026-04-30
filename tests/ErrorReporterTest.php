<?php

declare(strict_types=1);

namespace SlackErrorNotifier\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use SlackErrorNotifier\ErrorReporter;
use SlackErrorNotifier\SlackWebhookClient;

final class ErrorReporterTest extends TestCase
{
    public function testFormatsMessageWithHostDateAndError(): void
    {
        $client = new SlackWebhookClient('https://hooks.slack.com/services/fake/fake/fake');
        $reporter = new ErrorReporter($client);

        $message = $reporter->formatErrorMessage(new RuntimeException('Boom'), 'app-host');

        self::assertStringContainsString('Host: app-host', $message);
        self::assertStringContainsString('Date:', $message);
        self::assertStringContainsString('Error: Boom', $message);
    }
}
