# Deploying the mail path

Mail is queued, so **nothing is delivered until a worker is running**. Two pieces have to
exist on the server; neither is part of the application code.

## 1. Queue worker

Copy one of the two process definitions in this directory:

- `logixys-worker.service` — systemd (`systemctl enable --now logixys-worker`)
- `logixys-worker.supervisor.conf` — supervisor (`supervisorctl reread && supervisorctl update`)

Restart it on every deploy, after `php artisan migrate`, so the worker picks up new code:

```
php artisan queue:restart
```

## 2. Scheduler

One cron entry drives everything in `routes/console.php`:

```
* * * * * cd /var/www/logixys-cargo && php artisan schedule:run >> /dev/null 2>&1
```

## Environment

| Variable | Note |
| --- | --- |
| `APP_URL` | Must be the public domain. Every link in every email is built from it. |
| `MAIL_MAILER` | `resend` |
| `MAIL_FROM_ADDRESS` | Must sit on a domain verified in Resend, or sends are rejected. |
| `RESEND_API_KEY` | Send-only scope is enough. |
| `RESEND_WEBHOOK_SECRET` | From the Resend webhook page. Without it the endpoint returns 503 and bounces go unrecorded. |
| `QUEUE_CONNECTION` | `database` |

## Resend webhook

Point a Resend webhook at `https://<domain>/webhooks/resend` and subscribe to
`email.bounced` and `email.complained`. Addresses from those events land in
`mail_suppressions` and are skipped on later sends.

## When mail stops

A failed job emails the company inbox once per 30 minutes and logs at `critical`.

```
php artisan queue:failed      # what died
php artisan queue:retry all   # once the cause is fixed
```

`queue:prune-failed` clears anything older than two weeks, nightly.
