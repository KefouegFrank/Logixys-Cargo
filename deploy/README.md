# Deploying the mail path

This assumes a VPS with root — systemd or supervisor for the queue worker. Deploying to
shared cPanel hosting instead (no persistent processes, no root)? See
[`CPANEL.md`](CPANEL.md), which covers the whole deploy, not just mail.

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

## Before the first deploy

Run this on the server once the `.env` is in place, and again after every deploy:

```
php artisan deploy:check
```

It exits non-zero when the configuration is not safe to serve — debug mode left on, an
insecure session cookie, a placeholder password still able to sign in. Wire it into the
deploy script after `migrate` so a bad environment stops the release rather than reaching
the public.

The first administrator is created by hand, not seeded — there is no admin credential
anywhere in `.env`, a seeder, or this repo for the same reason there's no `password.txt`:

```
php artisan app:make-admin
```

It prompts for name, email and password and hashes the password straight into the
database, so it never sits in `.env`, a deploy log, or shell history. Answer the prompts
rather than passing `--password=` on the command line, which would land in shell history.
Everyone after the first admin is added from the panel.

## Environment

| Variable | Note |
| --- | --- |
| `APP_ENV` | `production`. |
| `APP_DEBUG` | `false`. With it on, any error page prints every credential below. |
| `LOG_LEVEL` | `error`. At `debug` the logs collect customer names and addresses. |
| `SESSION_SECURE_COOKIE` | `true`, so the panel session cookie is never sent in clear. |
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
