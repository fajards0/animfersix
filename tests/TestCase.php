<?php

namespace Tests;

use App\Models\ScraperSnapshot;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        if (Schema::hasTable('scraper_snapshots')) {
            ScraperSnapshot::query()->delete();
        }
    }
}
