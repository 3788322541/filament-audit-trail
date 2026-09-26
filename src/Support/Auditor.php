<?php

namespace Zhenjun\AuditTrail\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Zhenjun\AuditTrail\Events\AuditLogged;
use Zhenjun\AuditTrail\Models\AuditLog;

class Auditor
{
    public static function record(Model $model, string $event, array $old, array $new, array $exclude): void
    {
        if (AuditContext::isSilent()) {
            return;
        }

        if (app()->runningInConsole() && ! config('filament-audit-trail.record_console', true)) {
            return;
        }

        // An update where every changed attribute is excluded is a no-op.
        if ($event === 'updated' && $new === []) {
            return;
        }

        $actor = AuditContext::resolveActor();

        $tags = AuditContext::tags();

        if (app()->runningInConsole() && ! in_array('console', $tags, true)) {
            $tags[] = 'console';
        }

        $log = AuditLog::create([
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => (string) $model->getKey(),
            'auditable_name' => static::recordName($model, $exclude),
            'event' => $event,
            'old_values' => $old,
            'new_values' => $new,
            'actor_type' => $actor ? $actor::class : null,
            'actor_id' => $actor?->getAuthIdentifier(),
            'ip' => config('filament-audit-trail.record_ip', true) ? Request::ip() : null,
            'user_agent' => config('filament-audit-trail.record_user_agent', true) ? substr((string) Request::userAgent(), 0, 1023) : null,
            'url' => config('filament-audit-trail.record_url', true) ? (Request::fullUrl() ?: null) : null,
            'tags' => $tags ?: null,
        ]);

        event(new AuditLogged($log));
    }

    protected static function recordName(Model $model, array $exclude): ?string
    {
        if (method_exists($model, 'getAuditRecordName')) {
            return $model->getAuditRecordName();
        }

        $column = config('filament-audit-trail.record_name_column', 'name');

        $value = $model->getAttributeValue($column);

        return $value === null ? null : (string) $value;
    }
}
