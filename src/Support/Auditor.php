<?php

namespace Zhenjun\AuditTrail\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Zhenjun\AuditTrail\Contracts\AuditHasher;
use Zhenjun\AuditTrail\Contracts\FieldPolicy;
use Zhenjun\AuditTrail\Contracts\TeamResolver;
use Zhenjun\AuditTrail\Events\AuditLogged;
use Zhenjun\AuditTrail\Models\AuditLog;

class Auditor
{
    public static function record(Model $model, string $event, array $old, array $new, array $exclude): void
    {
        if (AuditContext::isSilent()) {
            return;
        }

        if (app()->runningInConsole() && !config('filament-audit-trail.record_console', true)) {
            return;
        }

        // An update where every changed attribute is excluded is a no-op.
        if ($event === 'updated' && $new === []) {
            return;
        }

        // Pro-tier hook: allow a whitelist / masking policy to reshape the diff.
        // The free binding is a pass-through, so this is a no-op by default.
        $diff = app(FieldPolicy::class)->apply($model, $event, ['old' => $old, 'new' => $new]);
        $old = $diff['old'] ?? [];
        $new = $diff['new'] ?? [];

        // If the policy filtered an update down to nothing, skip the write.
        if ($event === 'updated' && $old === [] && $new === []) {
            return;
        }

        $actor = AuditContext::resolveActor();

        $tags = AuditContext::tags();

        if (app()->runningInConsole() && !in_array('console', $tags, true)) {
            $tags[] = 'console';
        }

        $attributes = [
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
            // Pro-tier hook: multi-team / multi-tenant scoping. Null in the free tier.
            'team_id' => app(TeamResolver::class)->resolve($model, $actor),
        ];

        // Pro-tier hook: tamper-evident hash chain. Skipped entirely when disabled
        // (the free default) so no extra query runs on the write path.
        if (config('filament-audit-trail.pro.hashing.enabled', false)) {
            $previousHash = AuditLog::query()->latest('id')->value('hash');
            $attributes['hash'] = app(AuditHasher::class)->hash($attributes, $previousHash);
        }

        $log = AuditLog::create($attributes);

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
