# Running Downstream locally or on your own server

Downstream is an open source project with a Laravel API and a Vue frontend.
These instructions are for contributors and anyone hosting their own instance.
Use your own service credentials and domains. The checked-in frontend and Kamal
configuration include values for the public Downstream instance; replace them
when running a separate installation.

### Requirements

- PHP 8.2 or greater and Composer
- MySQL or MariaDB, with a database created for your instance
- Node.js 24.22.1 LTS or newer and pnpm 11.20.0
- A YouTube API key for search
- A Cloudflare Turnstile widget for account registration
- Optional Spotify credentials for Spotify integration

## API setup

```sh
git clone https://github.com/travierm/downstream.git
cd downstream
cp .env.example .env
composer install
php artisan key:generate
```

Edit `.env` with your database connection and `YOUTUBE_KEY`. For local
development, use the following application settings:

```dotenv
APP_URL=http://localhost:8000
APP_LINK_URL=http://localhost:5173
CACHE_DRIVER=file
```

Configure registration verification as described below, then start the API:

```sh
php artisan migrate
php artisan serve
```

## Frontend setup

Use Node.js 24.22.1 LTS or newer and pnpm 11.20.0 (`npm install --global pnpm@11.20.0`).
In a separate terminal, from the repository root:

```sh
cd vue_app
pnpm install --frozen-lockfile
cp .env.example .env.local
```

Set the frontend values in `vue_app/.env.local`:

```dotenv
VITE_API_URL=http://localhost:8000/api
VITE_TURNSTILE_SITE_KEY=your-widget-site-key
```

Start Vite with `pnpm dev` and open the local URL it prints. Restart Vite after
changing environment values. The frontend's `.env.local` is tracked in this
repository and must contain only public configuration.

## Registration verification

Registration is open to everyone and protected by Cloudflare Turnstile. Create
a widget for your instance, or reuse one you own. Configure its allowed hostnames
for your frontend, including `localhost` and `127.0.0.1` if you use it locally.
The public Downstream instance's widget is not intended for other installations.

Set the widget's public site key as `VITE_TURNSTILE_SITE_KEY` in the frontend.
Set its matching secret and the frontend hostname allowlist in the API's `.env`:

```dotenv
TURNSTILE_SECRET_KEY=your-widget-secret-key
TURNSTILE_HOSTNAMES=localhost,127.0.0.1
```

Hostnames must match the frontend exactly and contain no scheme, port, or path.
The API verifies the token, the `register` action, and the returned hostname
before creating an account. Registration is unavailable if verification is
unconfigured or cannot be completed.

Keep secrets in the API's ignored `.env` file or your deployment's secret store.
Never put the secret in frontend files, a `VITE_` variable, or version control.
Run `php artisan config:clear` after changing local API configuration.

See the [Turnstile documentation](https://developers.cloudflare.com/turnstile/)
for widget management and
[testing guidance](https://developers.cloudflare.com/turnstile/troubleshooting/testing/).

## Hosting your own instance

Configure the API for your production environment, including its database,
application key, service credentials, `APP_ENV=production`, and `APP_DEBUG=false`.
Use your own frontend hostname allowlist, for example:

```dotenv
TURNSTILE_HOSTNAMES=music.example.com
```

Production rejects local hostnames. Ensure your widget also allows your
production frontend hostname, and store its matching secret in the API's
deployment environment. Refresh Laravel's configuration cache with
`php artisan config:cache` when configuration changes.

Before serving updated API code, run `php artisan migrate --force`. Discover's
request deduplication uses cache locks; the database cache requires the
`cache_locks` table added by the migration. API workers must share the same
cache store for cross-worker deduplication.

To run migrations through Kamal, use `make migrate-prod`. This loads `.env.prod`
and runs migrations once on the primary host in the running API container.
That container must contain the migration files you intend to apply.

Discover caches completed recommendations for a day and individual Spotify to
YouTube matches for a day. Successful empty searches expire after five minutes;
provider failures are not cached as completed recommendations. Discover and
Daily Mix have separate cache entries for their requested result counts.
The new recommendation cache keys start fresh on deployment; existing entries
expire normally.
YouTube searches run at most four at a time with a ten-second timeout per search.
Set `DISCOVERY_YOUTUBE_CONCURRENCY=1` and refresh the configuration cache to
return to sequential searches. The frontend reuses results for sixty seconds
within the signed-in session and shares pending requests for the same track.

Signed-in users can open Daily Mix from the navigation menu on desktop or mobile.
The header also shows a music-note shortcut with a hover/focus tooltip when the
loaded collection contains at least one track added in the last 30 days.
`GET /api/daily-mix` serves the user's cached mix or generates one on demand under
a per-user lock, so it works without enabling the optional hydration schedule.
A mix lasts 24 hours from generation; collecting more music does not refresh a
cached mix immediately. Seeds come only from tracks collected in the past 30 days,
using up to six distinct tracks, newest first. Listening history is not used.
Each successfully used seed has its own three-day cooldown; other seeds' cooldowns
are not extended. If every eligible seed is on cooldown, the newest eligible
tracks can be reused. Removed tracks and older collections are excluded.
Each seed requests six recommendations. Empty recommendation results last five
minutes; failures do not advance cooldowns. Accounts without eligible collections
receive an uncached empty list with guidance to collect music.
Daily Mix cache keys are versioned to ignore older mixes and exclusion lists.
The page shows loading, empty, and retry states.

Build the frontend with your API URL and public site key:

```sh
cd vue_app
VITE_API_URL=https://api.example.com/api \
  VITE_TURNSTILE_SITE_KEY=your-widget-site-key pnpm build
```

Serve `vue_app/dist` with a fallback to `index.html` for client-side routes such
as `/register`. Frontend environment values are embedded during the build;
rebuild after changing them.

The repository includes Dockerfiles and Kamal configuration for the API and
frontend. If you use them, customize both `config/deploy.yml` files for your
servers, images, domains, and credentials. Set the API's `TURNSTILE_HOSTNAMES`
to your frontend domain; `.kamal/secrets` reads `TURNSTILE_SECRET_KEY` from your
deployment environment.

Before opening registration, verify that a fresh Turnstile token creates an
account and that reusing the token with a different registration is rejected.
Automated tests use mocked verification and do not validate your widget's live
configuration.

## Troubleshooting

Check your API URL, database connection, and service credentials first. For
registration failures, check that the frontend site key and API secret belong
to the same widget, that its allowed domains include your frontend hostname,
and that `TURNSTILE_HOSTNAMES` matches that hostname.

The [community wiki](https://github.com/travierm/downstream/wiki/Common-Setup-Issues)
also contains setup notes, though some may refer to older versions.

## Dictionary

### **Media**

Downstream isn't exclusively music so we refer to video's as media items. You'll see the word media a lot in the codebase it will always refer to a specific item with a media_id.

### **Collecting**

Users can collect any item available on Downstream or through organic search. It will then be displayed on their collection page.

### **Discovery**

No item exists on Downstream until a user discovers an item through search. That item is then used as a seed for our discovery service to find additional items related to what you collected.

These discovered items are temporary on Downstream until a user collects them and then they become a discovered media item on the site.

## Viewing Laravel logs

Use **View logs** in the Admin panel, or open `/log-viewer` on the Laravel API host
(production: `https://api.downstream.us/log-viewer`). Sign in at the browser prompt
with an existing admin account's email and password. The viewer and all its API
endpoints require an account with `type=admin`, including in local environments.
The browser prompt authenticates separately from the frontend's bearer-token login.
Use HTTPS in production.

OPcodes Log Viewer provides search, filters, and exception stack traces. The
`stack` logging channel writes to stderr and daily files in `storage/logs`, retained
for 14 days. Set `LOG_CHANNEL=stack` in existing environments so new application
logs appear in the viewer. Logs previously written only to stderr are not imported.
Kamal mounts a persistent volume for these files so they survive deployments.
Set `LOG_VIEWER_ENABLED=false` to disable the viewer. After changing environment
settings, rebuild any Laravel configuration cache with `php artisan config:cache`.
