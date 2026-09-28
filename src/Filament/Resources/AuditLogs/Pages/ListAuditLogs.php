<?php

namespace Zhenjun\AuditTrail\Filament\Resources\AuditLogs\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Zhenjun\AuditTrail\Filament\Resources\AuditLogs\AuditLogResource;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    /**
     * Widgets rendered above the table (e.g. the Pro chain status and stats
     * widgets). Populated by the Pro plugin via the package config.
     *
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    protected function getHeaderWidgets(): array
    {
        return config('filament-audit-trail.list_header_widgets', []);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
