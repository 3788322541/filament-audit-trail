<?php

namespace Zhenjun\AuditTrail\Filament\Resources\AuditLogs\Pages;

use Filament\Resources\Pages\ListRecords;
use Zhenjun\AuditTrail\Filament\Resources\AuditLogs\AuditLogResource;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
