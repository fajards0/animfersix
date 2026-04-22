# AnimeStream

Laravel anime streaming app with scraper-backed catalog, local snapshot fallback, and Render-ready deployment files.

## Render-ready changes

- scraper snapshots now use the database via `scraper_snapshots`
- Render deployment config is provided in [render.yaml](/c:/laragon/www/animestream/render.yaml)
- Docker runtime is provided in [Dockerfile](/c:/laragon/www/animestream/Dockerfile)
- scheduled snapshot warming is handled by:
  - `php artisan scraper:refresh-snapshots`
  - `php artisan scraper:refresh-snapshots --deep`

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan scraper:refresh-snapshots
php artisan serve
```

## Deploy to Render

1. Push this repo to GitHub.
2. Create a new Blueprint service in Render and point it to this repo.
3. Review env vars in `render.yaml`.
4. Deploy.

Important:
- set one shared `APP_KEY` secret in Render and use the same value for both the web service and cron service
- do not generate separate `APP_KEY` values per service

After the first deploy, Render will:
- run migrations
- warm core scraper snapshots
- expose the app as a web service
- run `schedule:run` from a cron service

## Important notes

- Render is a better fit than Netlify for this app because it needs PHP, database access, artisan commands, and cron.
- Scraper snapshot storage is database-backed so it survives Render redeploys without relying on local disk.
