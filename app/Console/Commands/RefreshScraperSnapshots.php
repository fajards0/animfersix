<?php

namespace App\Console\Commands;

use App\Services\OtakudesuApiService;
use Illuminate\Console\Command;

class RefreshScraperSnapshots extends Command
{
    protected $signature = 'scraper:refresh-snapshots {--path=* : Refresh only specific scraper paths} {--deep : Warm more public genre pages} {--limit=0 : Only process the first N generated paths}';

    protected $description = 'Refresh local scraper snapshots for public AnimeStream pages.';

    public function handle(OtakudesuApiService $service): int
    {
        $paths = $this->option('path');

        if ($paths === []) {
            $paths = $service->warmSnapshotPaths((bool) $this->option('deep'));
        }

        $limit = max(0, (int) $this->option('limit'));

        if ($limit > 0) {
            $paths = array_slice($paths, 0, $limit);
        }

        $this->info('Refreshing scraper snapshots...');

        $results = $service->refreshSnapshots($paths);

        foreach ($results as $path => $status) {
            $line = str_starts_with($status, 'ok')
                ? "<info>OK</info> {$path}"
                : "<comment>{$status}</comment> {$path}";

            $this->line($line);
        }

        return self::SUCCESS;
    }
}
