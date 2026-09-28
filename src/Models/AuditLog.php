<?php

namespace Zhenjun\AuditTrail\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

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
     * The team / tenant this entry belongs to.
     *
     * Resolvable once a team model is known: either via the
     * `filament-audit-trail.team_model` config key or via the current
     * Filament panel's tenant model.
     */
    public function team(): BelongsTo
    {
        $model = static::teamModel();

        if ($model === null) {
            throw new LogicException(
                'Unable to resolve the audit log team model. Set the [filament-audit-trail.team_model] config key to your team model class, or enable tenancy on a Filament panel that registers the audit log resource.'
            );
        }

        return $this->belongsTo($model, 'team_id');
    }

    public static function teamModel(): ?string
    {
        $model = config('filament-audit-trail.team_model');

        if (is_string($model) && $model !== '') {
            return $model;
        }

        if (app()->bound('filament')) {
            return Filament::getCurrentPanel()?->getTenantModel();
        }

        return null;
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
