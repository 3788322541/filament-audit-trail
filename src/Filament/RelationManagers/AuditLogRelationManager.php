<?php

namespace Zhenjun\AuditTrail\Filament\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AuditLogRelationManager extends RelationManager
{
    protected static string $relationship = 'auditLogs';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-clock';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament-audit-trail::audit.plural');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('filament-audit-trail::audit.plural'))
            ->columns([
                TextColumn::make('event')
                    ->label(__('filament-audit-trail::audit.fields.event'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'restored' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("filament-audit-trail::audit.events.{$state}")),
                TextColumn::make('actor.name')
                    ->label(__('filament-audit-trail::audit.fields.actor'))
                    ->default('—'),
                TextColumn::make('created_at')
                    ->label(__('filament-audit-trail::audit.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->paginated([10, 25, 50]);
    }
}
