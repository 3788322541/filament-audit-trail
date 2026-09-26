@php
/** @var array $changes Merged field-level changes from the AuditLog::changes accessor. */
$changes = $state ?? [];
@endphp

<div>
    @if (empty($changes))
    <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ __('filament-audit-trail::audit.diff.no_changes') }}
    </p>
    @else
    <div class="grid gap-2">
        @foreach ($changes as $change)
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-white/10">
            <div class="bg-gray-50 px-3 py-1.5 text-xs font-medium text-gray-700 dark:bg-white/5 dark:text-gray-200">
                {{ $change['key'] }}
            </div>
            <div class="grid grid-cols-1 divide-y divide-gray-200 text-sm sm:grid-cols-2 sm:divide-x sm:divide-y-0 dark:divide-white/10">
                <div class="p-3">
                    <div class="mb-1 text-xs font-medium text-danger-600 dark:text-danger-400">
                        {{ __('filament-audit-trail::audit.diff.old') }}
                    </div>
                    <div class="break-words font-mono text-xs text-gray-700 dark:text-gray-200">
                        {{ is_scalar($change['old']) || $change['old'] === null ? ($change['old'] ?? '—') : json_encode($change['old'], JSON_UNESCAPED_UNICODE) }}
                    </div>
                </div>
                <div class="p-3">
                    <div class="mb-1 text-xs font-medium text-success-600 dark:text-success-400">
                        {{ __('filament-audit-trail::audit.diff.new') }}
                    </div>
                    <div class="break-words font-mono text-xs text-gray-700 dark:text-gray-200">
                        {{ is_scalar($change['new']) || $change['new'] === null ? ($change['new'] ?? '—') : json_encode($change['new'], JSON_UNESCAPED_UNICODE) }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>