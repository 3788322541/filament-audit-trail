<?php

namespace Zhenjun\AuditTrail;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Zhenjun\AuditTrail\Commands\PurgeAuditLogs;
use Zhenjun\AuditTrail\Contracts\AuditHasher;
use Zhenjun\AuditTrail\Contracts\FieldPolicy;
use Zhenjun\AuditTrail\Contracts\TeamResolver;
use Zhenjun\AuditTrail\Support\Defaults\NullAuditHasher;
use Zhenjun\AuditTrail\Support\Defaults\NullTeamResolver;
use Zhenjun\AuditTrail\Support\Defaults\PassThroughFieldPolicy;

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
        // Pro-tier extension points. The free package ships null / pass-through
        // implementations so behaviour is unchanged until zhenjun/filament-audit-trail-pro
        // rebinds these to real implementations. Use `bind` (not `singleton`) so
        // the Pro package can override cleanly via `$this->app->singleton(...)`.
        $this->app->bind(AuditHasher::class, NullAuditHasher::class);
        $this->app->bind(TeamResolver::class, NullTeamResolver::class);
        $this->app->bind(FieldPolicy::class, PassThroughFieldPolicy::class);
    }

    public function packageBooted(): void
    {
        // Migrations ship inside the package and run automatically (zero-config).
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
