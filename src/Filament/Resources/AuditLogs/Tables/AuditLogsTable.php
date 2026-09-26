<?php

namespace Zhenjun\AuditTrail\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event')
                    ->label(__('filament-audit-trail::audit.fields.event'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'restored' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => __("filament-audit-trail::audit.events.{$state}")),
                TextColumn::make('auditable_name')
                    ->label(__('filament-audit-trail::audit.fields.auditable'))
                    ->searchable()
                    ->description(fn(mixed $record): string => (new \ReflectionClass($record->auditable_type))->getShortName())
                    ->wrap(),
                TextColumn::make('actor.name')
                    ->label(__('filament-audit-trail::audit.fields.actor'))
                    ->default('—'),
                TextColumn::make('changes_count')
                    ->label(__('filament-audit-trail::audit.fields.changes'))
                    ->state(fn(mixed $record): int => count($record->changes))
                    ->suffix(' field(s)'),
                TextColumn::make('created_at')
                    ->label(__('filament-audit-trail::audit.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->label(__('filament-audit-trail::audit.fields.event'))
                    ->options([
                        'created' => __('filament-audit-trail::audit.events.created'),
                        'updated' => __('filament-audit-trail::audit.events.updated'),
                        'deleted' => __('filament-audit-trail::audit.events.deleted'),
                        'restored' => __('filament-audit-trail::audit.events.restored'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([])
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateDescription('No audit activity has been recorded yet.');
    }
}
