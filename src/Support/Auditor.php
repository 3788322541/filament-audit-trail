<?php

namespace Zhenjun\AuditTrail\Support;

use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
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

        // Pro-tier hook: multi-team / multi-tenant scoping. Null in the free tier.
        $teamId = app(TeamResolver::class)->resolve($model, $actor);

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
            'team_id' => $teamId,
        ];

        // Pro-tier hook: tamper-evident hash chain. Skipped entirely when disabled
        // (the free default) so no extra query runs on the write path.
        $log = config('filament-audit-trail.pro.hashing.enabled', false)
            ? static::createWithHash($attributes, $teamId)
            : AuditLog::create($attributes);

        event(new AuditLogged($log));
    }

    /**
     * Write a hash-chained entry. The chain is tracked per team: the previous
     * hash is looked up among the same team's hashed entries (null team
     * included), ignoring global scopes so tenancy filters cannot hide the
     * real tail of the chain.
     *
     * When the cache store supports atomic locks, the lookup + insert pair is
     * serialized so concurrent requests cannot fork the chain. A lock timeout
     * degrades to an unserialized write rather than failing the caller's
     * business operation.
     */
    protected static function createWithHash(array $attributes, int|string|null $teamId): AuditLog
    {
        $write = static function () use ($attributes, $teamId): AuditLog {
            $query = AuditLog::withoutGlobalScopes()->whereNotNull('hash');

            $query = $teamId === null
                ? $query->whereNull('team_id')
                : $query->where('team_id', $teamId);

            $attributes['hash'] = app(AuditHasher::class)->hash($attributes, $query->latest('id')->value('hash'));

            return AuditLog::create($attributes);
        };

        if (Cache::getStore() instanceof LockProvider) {
            try {
                return Cache::lock('filament-audit-trail:hash-chain:' . ($teamId ?? 'global'), 10)->block(5, $write);
            } catch (LockTimeoutException) {
                // Degrade to an unserialized write rather than failing the
                // caller's business operation. The worst case is a forked
                // chain, which the verifier will surface.
            }
        }

        return $write();
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
