<?php

namespace Zhenjun\AuditTrail\Filament\Resources\AuditLogs\Pages;

use Filament\Resources\Pages\ViewRecord;
use Zhenjun\AuditTrail\Filament\Resources\AuditLogs\AuditLogResource;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;
}
