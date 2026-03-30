<?php

namespace Tests;

use PDO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        if ($this->shouldSkipForMissingSqliteDriver()) {
            $this->markTestSkipped(
                'Test ini butuh SQLite untuk mode testing (DB_CONNECTION=sqlite), tapi ekstensi pdo_sqlite belum aktif.'
            );
        }

        parent::setUp();
    }

    private function shouldSkipForMissingSqliteDriver(): bool
    {
        if (!$this->testUsesDatabase()) {
            return false;
        }

        $dbConnection = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? null;
        $availableDrivers = PDO::getAvailableDrivers();

        return $dbConnection === 'sqlite' && !in_array('sqlite', $availableDrivers, true);
    }

    private function testUsesDatabase(): bool
    {
        $traits = class_uses_recursive(static::class);

        return in_array(RefreshDatabase::class, $traits, true)
            || in_array(DatabaseMigrations::class, $traits, true)
            || in_array(DatabaseTruncation::class, $traits, true);
    }
}
