# Currency Rates

A small Laravel app that tracks the GBP/EUR and EUR/GBP exchange rates over
time and plots them as a line chart.

Rate data comes from [Frankfurter](https://www.frankfurter.app), a free API
serving European Central Bank reference rates. It needs **no API key** and no
account, which keeps this project runnable from a fresh clone.

## How it works

```
app/Console/Commands/PopulateRateData.php   get:rates — backfills both pairs
app/Services/CurrencyRateService.php        fetches a date range, stores new rows
app/Http/Controllers/CurrencyController.php groups rows into one series per pair
resources/views/dashboard.blade.php         Chart.js, one dataset per pair
bootstrap/app.php                           routes + the daily schedule
```

`get:rates` fetches a whole date range in a **single** HTTP request per pair
rather than one request per day, then inserts only the dates it does not
already have. That makes it cheap to run repeatedly and safe to schedule.

Two details worth knowing, because both are easy to get wrong:

- **The response is keyed by the date the rate was actually published.** The
  ECB does not publish at weekends or on holidays, so those dates are simply
  absent and the series has natural gaps. Requesting a Saturday returns the
  preceding Friday's rate.
- **A 4xx response is not retried.** It means there is no data for the
  requested window, whereas a 5xx or a dropped connection is transient and is
  retried. Failures return an empty set and are logged rather than thrown, so
  one bad response cannot abort the other pair.

## Requirements

- PHP 8.3+
- Composer
- Node 20+ (only to build the front-end assets)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

The last two commands compile the CSS and JavaScript with Vite into
`public/build`, which is gitignored. Without them the dashboard will render
un-styled and the chart will not draw. During development, `npm run dev` starts
Vite with hot reloading instead.

Point the database at whatever you like in `.env`. The default configuration
expects the bundled [Laravel Sail](https://laravel.com/docs/sail) MySQL
container:

```bash
docker compose up -d
php artisan migrate
```

Prefer SQLite? Then set `DB_CONNECTION=sqlite` and create an empty file at
`database/database.sqlite`.

## Populating data

The chart is only as fresh as the last run, so populate it once to begin with:

```bash
php artisan get:rates              # backfills the last 30 days (default)
php artisan get:rates --days=90   # or a longer window
```

Each scheduled run fetches only the default 30-day window, so it stays fast no
matter how far back the data goes — it tops the window up rather than starting
over. Business days only, so expect roughly 21 rows per pair for a 30-day
window.

## Keeping it fresh

`get:rates` is scheduled to run daily at 06:00 in `bootstrap/app.php`, with
`withoutOverlapping()` so concurrent runs cannot race. Run Laravel's scheduler
to make that happen:

```bash
php artisan schedule:work     # for local development
```

In production, add the usual cron entry:

```
* * * * * cd /path/to/currency && php artisan schedule:run >> /dev/null 2>&1
```

## Tests

The suite runs against an in-memory SQLite database, so it needs no setup and
no running database:

```bash
composer test
```

Static analysis and code style:

```bash
./vendor/bin/phpstan analyse    # level 5
./vendor/bin/pint --test
```

## Notes

- The `rate` column is a `float`. That is ample for exchange rates in this
  range; switch to `decimal` if you ever need exact arithmetic.
- Rows are soft deleted, and a soft-deleted row still counts as "already
  recorded" when backfilling, so re-running the command will not resurrect or
  duplicate it.
- Chart.js is bundled into the JavaScript at build time, so the page has no
  external runtime dependency and works offline.

## Licence

MIT.
