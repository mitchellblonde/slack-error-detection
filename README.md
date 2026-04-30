# Slack Error Notifier

Een Composer package voor **PHP en Laravel** dat errors naar een Slack-kanaal stuurt via een Incoming Webhook.

Berichtformaat:

- Host
- Date
- Error

## Installatie

```bash
composer require mitchellblonde/slack-error-detection


## Configuratie (.env)

Voeg dit toe aan je `.env`:

```env
SLACK_ERROR_WEBHOOK_URL=https://hooks.slack.com/services/XXX/YYY/ZZZ
SLACK_ERROR_TIMEOUT_SECONDS=2.5
```

> Gebruik je echte webhook alleen in `.env` (niet hardcoden in code).

## Laravel gebruik

De ServiceProvider wordt automatisch ontdekt via package discovery.

### Stap 1: trait toevoegen aan `app/Exceptions/Handler.php`

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

Vanaf dat moment wordt elke gerapporteerde exception ook naar Slack gestuurd.

## Plain PHP gebruik

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

## Voorbeeld Slack payload (zelfde manier als curl)

De package verstuurt JSON in exact dezelfde stijl als:

```bash
curl -X POST -H 'Content-type: application/json' \
--data '{"text":"Host: my-server\nDate: 2026-04-30T12:00:00+00:00\nError: Something went wrong"}' \
https://hooks.slack.com/services/XXX/YYY/ZZZ
```

## Veiligheid

- Zet webhook URL altijd in `.env`.
- Commit nooit echte Slack webhook tokens naar git.
