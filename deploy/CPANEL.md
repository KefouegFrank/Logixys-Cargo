# Deploying to cPanel (Asura Hosting or similar)

This is for shared/reseller cPanel hosting: no root, no systemd, no supervisor — Apache,
not nginx, in front. `README.md` in this directory assumes a VPS; the queue-worker and
scheduler steps here are different because of that, everything else is the same
application. Read `README.md`'s Resend and "when mail stops" sections too — they're not
repeated here.

## 0. Before you start

- A cPanel login for the hosting account, with the domain already added (primary or addon).
- The GitHub repo URL. If it's private, either a personal access token or an SSH deploy key
  — decide before step 3.
- A [Resend](https://resend.com) account with the sending domain verified, an API key, and
  a webhook secret (`README.md` → Resend webhook).
- Geoapify and LocationIQ API keys are optional — the address autocomplete on the booking
  form works with no key at all (BAN and Photon), those two are only fallbacks.

## 1. Database

cPanel → **MySQL® Databases** → create a database, create a user, add the user to the
database with all privileges. cPanel prefixes both names with the cPanel username (e.g.
`cpuser_logixys`, `cpuser_logixys_admin`) — write down the full prefixed names, you'll need
them in step 7.

## 2. PHP version

cPanel → **MultiPHP Manager** (or **Select PHP Version**) → set **PHP 8.3** or later for
the domain. Do this before step 5 — Composer will refuse to install against the wrong
version.

## 3. Get the code onto the server

**cPanel's own Git tool** (Git™ Version Control icon) is the easiest path for a GitHub repo:

1. Create → paste the repo's clone URL.
   - Private repo over HTTPS: `https://<token>@github.com/you/logixys-cargo.git`, using a
     GitHub personal access token in place of a password.
   - Private repo over SSH: add an SSH key in cPanel first (**SSH Access** → generate or
     upload a key), add its public half as a deploy key on the GitHub repo, then use the
     `git@github.com:...` URL.
2. **Set the repository path outside `public_html`** — e.g. `logixys-cargo` in the account's
   home directory, not `public_html/logixys-cargo`. Nothing except `public/` is meant to be
   web-reachable; the whole point of `README.md`'s note that invoices live outside the web
   root falls apart if the rest of the app sits inside it too.

No Git Version Control tool available? The same thing from **Terminal** (or SSH):

```
cd ~
git clone https://github.com/you/logixys-cargo.git
```

## 4. Point the domain at `public/`

cPanel → **Domains** → the domain → check whether **Document Root** is editable.

**If it is:** set it to `/home/<user>/logixys-cargo/public`. Done — skip to step 5.

**If it's locked to `public_html`** (some shared plans do this): the standard workaround —

1. Copy the *contents* of `logixys-cargo/public/` into `public_html/` (File Manager, or
   `cp -r ~/logixys-cargo/public/* ~/public_html/` in Terminal) — not the `public` folder
   itself, what's inside it.
2. Edit `public_html/index.php`: it has two `require` lines pointing at
   `__DIR__.'/../vendor/autoload.php'` and `__DIR__.'/../bootstrap/app.php'`. Change the
   `..` to reach the real app instead, e.g. `__DIR__.'/../logixys-cargo/vendor/autoload.php'`.
3. Repeat step 1 after every future front-end build (step 6 regenerates `public/build`) —
   worth scripting if you're staying on this path long-term.

## 5. Install PHP dependencies

Terminal (or SSH):

```
cd ~/logixys-cargo
composer install --no-dev --optimize-autoloader
```

No `composer` on the PATH? Check whether WHM has the Composer plugin installed, or download
`composer.phar` into the account and run `php composer.phar install ...` instead. If
Composer runs out of memory on a constrained plan: prefix the command with
`COMPOSER_MEMORY_LIMIT=-1`.

This also runs `filament:upgrade` and `package:discover` automatically — nothing extra
needed for Filament itself.

## 6. Build the front-end assets

Check whether Node is available on the server first:

```
node -v
npm -v
```

**If both work:**

```
npm install
npm run build
```

This needs outbound internet access during the build — the panel's theme self-hosts two
font families from Bunny Fonts at build time, not at request time.

**If Node isn't available** (common on shared plans): build on your own machine from the
same commit instead —

```
npm install
npm run build
```

— then upload the resulting `public/build/` folder to the server (File Manager or SFTP),
into whichever `public/` step 4 left as the real one. It's static output; only redo this
when CSS or JS actually changes.

Skip either path and the site loads with no styling and a broken map/barcode — that's the
one symptom that means "assets never got built."

## 7. The `.env` file

Not in the repo — create it from the example and fill it in:

```
cp .env.example .env
```

```env
APP_NAME="Logixys Cargo"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.tld

APP_LOCALE=fr
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cpuser_logixys
DB_USERNAME=cpuser_logixys
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true

# Correct as-is for a typical cPanel/Apache setup, where Apache terminates TLS directly —
# there's no separate proxy in front, so nothing needs to be added here. The one case that
# changes this: the site sits behind Cloudflare (or another CDN/proxy). Then set this to
# Cloudflare's published IP ranges instead of the loopback default, or the real client
# address is lost the same way it was before this app's F-4 fix.
TRUSTED_PROXIES=127.0.0.1,::1

CONTACT_TRIAGE_RETENTION_DAYS=30

QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=resend
RESEND_API_KEY=
RESEND_WEBHOOK_SECRET=
MAIL_FROM_ADDRESS="info@your-domain.tld"
MAIL_FROM_NAME="${APP_NAME}"

# BAN and Photon (France's own address APIs) need no key and are the primary lookup.
# These two are fallbacks only — leave the keys blank if you don't have them.
ADDRESS_SEARCH_PRIMARY=ban,photon
ADDRESS_SEARCH_FALLBACK=geoapify,locationiq
GEOAPIFY_API_KEY=
LOCATIONIQ_API_KEY=

GEOCODER_PROVIDER=nominatim
GEOCODER_USER_AGENT="LogixysCargo/1.0 (info@your-domain.tld)"
```

A few things worth knowing about this list:

- **Redis is not needed.** Session, cache and queue all run on the `database` driver — one
  MySQL database covers everything, no extra service to provision.
- `AWS_*` and `MEMCACHED_*` in `.env.example` are Laravel's stock scaffold, unused by this
  app — leave them out.
- Nominatim needs no API key; a `GEOCODER_API_KEY` line does nothing even if you set one.
- `TRACKING_PREFIX`, `RECEIPT_PREFIX`, `DEFAULT_CURRENCY`, `DEFAULT_TAX_RATE`,
  `DEFAULT_TAX_LABEL` and `RETENTION_YEARS` are not read anywhere in this codebase — they
  won't hurt if set, but setting them changes nothing.

## 8. Generate the app key

```
php artisan key:generate
```

## 9. Run the migrations

```
php artisan migrate --force
```

## 10. Create the first admin

There's no seeded or env-var admin account — deliberately, see `README.md`. Create the real
one interactively instead:

```
php artisan app:make-admin
```

cPanel's Terminal is a real interactive session, so the prompts (including the hidden
password entry) work normally — answer them rather than passing `--email=`/`--password=` as
flags, which would put the password in shell history.

## 11. Verify the configuration is actually safe

```
php artisan deploy:check
```

Every line has to say **OK** before this is safe to put in front of real traffic — it
catches exactly the kind of thing easy to get wrong copying an `.env` by hand: debug mode
left on, an insecure session cookie, a leftover weak password.

## 12. Cache for production

```
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Skip `storage:link` — nothing in this app serves files through the public disk (invoices
are deliberately kept off it, outside the web root).

## 13. Cron Jobs — replacing systemd/supervisor

cPanel → **Cron Jobs** → add two entries. This is the one real structural difference from
`README.md`'s VPS instructions: there's no persistent worker process on shared hosting, so
the queue runs in short bursts instead.

**Scheduler** — drives `queue:prune-failed` and `contact-messages:prune`:

```
* * * * * cd /home/<user>/logixys-cargo && php artisan schedule:run >> /dev/null 2>&1
```

**Queue worker** — mail is queued so a slow Resend never blocks a response; nothing sends
until something processes the queue:

```
* * * * * cd /home/<user>/logixys-cargo && php artisan queue:work --stop-when-empty --max-time=55 >> /dev/null 2>&1
```

Each run picks up whatever's queued and exits before the next one starts, so there's no
`queue:restart` step to remember on redeploy — a fresh cron minute already runs whatever
code is currently on disk.

## 14. SSL

cPanel → **SSL/TLS Status** → run **AutoSSL** if it hasn't already issued a certificate.
Confirm `https://your-domain.tld` actually loads before relying on `SESSION_SECURE_COOKIE`
being set — a secure cookie on a site not actually served over HTTPS just means the cookie
never gets sent at all, which looks like "nobody can stay logged in."

## 15. Resend webhook

Point a Resend webhook at `https://your-domain.tld/webhooks/resend`, subscribed to
`email.bounced` and `email.complained` — see `README.md` for what this does.

## 16. Final check

- `php artisan deploy:check` — every line OK.
- Book a test shipment in the panel; confirm both parties and the office get mail (proves
  the cron-based queue worker is actually running).
- Submit the public contact form; confirm the acknowledgement and the internal copy arrive.
- Open `/fr/suivi/<a real tracking number>`; confirm the barcode and the map render (proves
  step 4 and step 6 both landed correctly).

## Redeploying after this

```
cd ~/logixys-cargo
git pull
composer install --no-dev --optimize-autoloader
npm run build          # or re-upload public/build if Node isn't on the server — step 6
php artisan migrate --force
php artisan config:cache
php artisan deploy:check
```

No `queue:restart` — see step 13.

## Troubleshooting

- **500 error, blank page.** `APP_DEBUG=false` hides the detail from the browser on
  purpose; the real error is in `storage/logs/laravel.log`.
- **"The stream or file ... could not be opened."** `storage/` and `bootstrap/cache/` need
  to be writable by the account: `chmod -R 775 storage bootstrap/cache`.
- **Map, barcode, or styling missing.** Step 4 (document root) or step 6 (asset build)
  didn't land — check which `public/` is actually being served.
- **`deploy:check` fails on trusted proxies.** Only expected if the site sits behind
  Cloudflare or another proxy — see the `TRUSTED_PROXIES` note in step 7.
- **Rate limits (tracking lookup, contact form, login) triggering for every visitor at
  once, or the wrong `ip_address` on contact messages.** Same cause as the item above.
