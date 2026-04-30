<?php

declare(strict_types=1);

namespace SlackErrorNotifier\Laravel;

use SlackErrorNotifier\ErrorReporter;
use Throwable;

trait ReportsToSlack
{
    public function report(Throwable $e): void
    {
        parent::report($e);

        if (!app()->bound(ErrorReporter::class)) {
            return;
        }

        app(ErrorReporter::class)->report($e);
    }
}
