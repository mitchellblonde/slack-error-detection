<?php

declare(strict_types=1);

namespace SlackErrorNotifier\Laravel;

use Illuminate\Support\ServiceProvider;
use SlackErrorNotifier\ErrorReporter;
use SlackErrorNotifier\SlackWebhookClient;

final class SlackErrorNotifierServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SlackWebhookClient::class, static function () {
            /** @var string $webhook */
            $webhook = (string) env('SLACK_ERROR_WEBHOOK_URL', '');
            $timeout = (float) env('SLACK_ERROR_TIMEOUT_SECONDS', 2.5);

            return new SlackWebhookClient($webhook, $timeout);
        });

        $this->app->singleton(ErrorReporter::class, static fn ($app) => new ErrorReporter($app->make(SlackWebhookClient::class)));
    }
}
