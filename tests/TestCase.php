<?php

namespace Zhenjun\AuditTrail\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Zhenjun\AuditTrail\AuditTrailServiceProvider;
use Zhenjun\AuditTrail\Support\AuditContext;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->resetAuditContext();
    }

    /**
     * AuditContext stores actor/silent/tags in static state, which would
     * otherwise leak between tests within the same process.
     */
    protected function resetAuditContext(): void
    {
        $defaults = [
            'actor' => null,
            'isSilent' => false,
            'tags' => [],
        ];

        $reflection = new \ReflectionClass(AuditContext::class);

        foreach ($defaults as $property => $value) {
            $target = $reflection->getProperty($property);
            $target->setAccessible(true);
            $target->setValue(null, $value);
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            AuditTrailServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        // Exercise the real package migration (audit_logs).
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }
}
