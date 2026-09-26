<?php

namespace Zhenjun\AuditTrail\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('event')
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
                        TextEntry::make('created_at')
                            ->label(__('filament-audit-trail::audit.fields.created_at'))
                            ->dateTime(),
                        TextEntry::make('auditable_name')
                            ->label(__('filament-audit-trail::audit.fields.auditable'))
                            ->default('—'),
                        TextEntry::make('actor.name')
                            ->label(__('filament-audit-trail::audit.fields.actor'))
                            ->default('—'),
                        TextEntry::make('ip')
                            ->label(__('filament-audit-trail::audit.fields.ip'))
                            ->default('—'),
                        TextEntry::make('url')
                            ->label(__('filament-audit-trail::audit.fields.url'))
                            ->default('—')
                            ->url(fn(?string $state): ?string => $state)
                            ->openUrlInNewTab(),
                    ]),
                Section::make(__('filament-audit-trail::audit.fields.changes'))
                    ->schema([
                        Entry::make('changes')
                            ->hiddenLabel()
                            ->view('filament-audit-trail::infolists.changes')
                            ->viewData(fn($record): array => ['changes' => $record->changes]),
                    ]),
            ]);
    }
}
