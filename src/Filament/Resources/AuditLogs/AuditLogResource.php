<?php

namespace Zhenjun\AuditTrail\Filament\Resources\AuditLogs;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Zhenjun\AuditTrail\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use Zhenjun\AuditTrail\Filament\Resources\AuditLogs\Pages\ViewAuditLog;
use Zhenjun\AuditTrail\Filament\Resources\AuditLogs\Schemas\AuditLogInfolist;
use Zhenjun\AuditTrail\Filament\Resources\AuditLogs\Tables\AuditLogsTable;
use Zhenjun\AuditTrail\Models\AuditLog;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 90;

    /**
     * Scope the resource to the current Filament tenant via the model's
     * `team()` relationship. No-op when the panel has tenancy disabled.
     */
    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function getNavigationLabel(): string
    {
        return __('filament-audit-trail::audit.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament-audit-trail::audit.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-audit-trail::audit.plural');
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
            'view' => ViewAuditLog::route('/{record}'),
        ];
    }
}
