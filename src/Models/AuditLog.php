<?php

namespace Zhenjun\AuditTrail\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    /**
     * The audit log is append-only, so it has no "updated_at" column.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'auditable_name',
        'event',
        'old_values',
        'new_values',
        'actor_type',
        'actor_id',
        'ip',
        'user_agent',
        'url',
        'tags',
        'team_id',
        'hash',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'tags' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): MorphTo
    {
        return $this->morphTo('actor');
    }

    /**
     * A merged, ordered list of changed fields with their old and new values.
     *
     * @return array<int, array{key: string, old: mixed, new: mixed}>
     */
    public function getChangesAttribute(): array
    {
        $old = (array) ($this->old_values ?? []);
        $new = (array) ($this->new_values ?? []);

        $changes = [];

        foreach (array_unique(array_merge(array_keys($old), array_keys($new))) as $key) {
            $changes[] = [
                'key' => $key,
                'old' => $old[$key] ?? null,
                'new' => $new[$key] ?? null,
            ];
        }

        return $changes;
    }

    public function scopeEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    public function scopeForRecord(Builder $query, string $type, string|int $id): Builder
    {
        return $query->where('auditable_type', $type)->where('auditable_id', (string) $id);
    }
}
