<?php

namespace Zhenjun\AuditTrail;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Zhenjun\AuditTrail\Commands\PurgeAuditLogs;

class AuditTrailServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-audit-trail';

    public static string $viewNamespace = 'filament-audit-trail';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasCommand(PurgeAuditLogs::class)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews(static::$viewNamespace)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('3788322541/filament-audit-trail');
            });
    }

    public function packageRegistered(): void
    {
        //
    }

    public function packageBooted(): void
    {
        // Migrations ship inside the package and run automatically (zero-config).
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
