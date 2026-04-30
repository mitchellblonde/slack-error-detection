# Slack Error Notifier

A Composer package for **PHP and Laravel** that sends application errors to a Slack channel through an Incoming Webhook.

## Message format

Each Slack message includes:

- Host
- Website
- User (if authenticated)
- Date
- File
- Line
- Error

## Installation

```bash
composer require mitchellblonde/slack-error-detection
```

## Environment configuration

Add these values to your `.env` file:

```env
SLACK_ERROR_WEBHOOK_URL=https://hooks.slack.com/services/XXX/YYY/ZZZ
SLACK_ERROR_TIMEOUT_SECONDS=2.5
APP_URL=https://example.com
```

> Store your real webhook token only in `.env` and never hardcode it.

## Laravel usage

The package uses Laravel auto-discovery for its ServiceProvider.

### Step 1: use the trait in `app/Exceptions/Handler.php`

```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use SlackErrorNotifier\Laravel\ReportsToSlack;

class Handler extends ExceptionHandler
{
    use ReportsToSlack;
}
```

From that point on, every reported exception is also sent to Slack.

### User resolution behavior

- In Laravel, the package tries `auth()->user()` first.
- In plain PHP, it tries `$_SESSION['user']`.
- If no user is available, it sends `User: guest`.

## Plain PHP usage

```php
<?php

use SlackErrorNotifier\ErrorReporter;
use SlackErrorNotifier\SlackWebhookClient;

$client = new SlackWebhookClient(getenv('SLACK_ERROR_WEBHOOK_URL'));
$reporter = new ErrorReporter($client);

try {
    throw new RuntimeException('Test error');
} catch (Throwable $e) {
    $reporter->report($e);
}
```

## Example Slack payload (same style as curl)

```bash
curl -X POST -H 'Content-type: application/json' \
--data '{"text":"Host: my-server\nWebsite: https://example.com\nUser: 123\nDate: 2026-04-30T12:00:00+00:00\nFile: /var/www/app/Service.php\nLine: 42\nError: Something went wrong"}' \
https://hooks.slack.com/services/XXX/YYY/ZZZ
```

## Security notes

- Keep the webhook URL in `.env`.
- Never commit real Slack webhook tokens.
