<?php

namespace Zhenjun\AuditTrail\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Zhenjun\AuditTrail\Support\Auditor;
use Zhenjun\AuditTrail\Support\DiffRecorder;

trait Auditable
{
    /**
     * @var array{event: string, old: array, new: array}|null
     */
    protected ?array $pendingAudit = null;

    public static function bootAuditable(): void
    {
        static::created(fn(Model $model) => $model->writeAudit('created'));
        static::updating(fn(Model $model) => $model->prepareUpdateAudit());
        static::updated(fn(Model $model) => $model->writeAudit('updated'));
        static::deleting(fn(Model $model) => $model->prepareDeleteAudit());
        static::deleted(fn(Model $model) => $model->writeAudit('deleted'));

        // The "restored" event only exists on models that use SoftDeletes.
        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            static::restored(fn(Model $model) => $model->writeAudit('restored'));
        }
    }

    protected function prepareUpdateAudit(): void
    {
        [$old, $new] = DiffRecorder::forUpdate($this, $this->getAuditExcludes());

        $this->pendingAudit = ['event' => 'updated', 'old' => $old, 'new' => $new];
    }

    protected function prepareDeleteAudit(): void
    {
        $this->pendingAudit = [
            'event' => 'deleted',
            'old' => DiffRecorder::snapshot($this, $this->getAuditExcludes()),
            'new' => [],
        ];
    }

    protected function writeAudit(string $event): void
    {
        if ($event === 'created') {
            $old = [];
            $new = DiffRecorder::snapshot($this, $this->getAuditExcludes());
        } elseif ($event === 'restored') {
            $old = [];
            $new = [];
        } else {
            $old = $this->pendingAudit['old'] ?? [];
            $new = $this->pendingAudit['new'] ?? [];
        }

        $this->pendingAudit = null;

        Auditor::record($this, $event, $old, $new, $this->getAuditExcludes());
    }

    /**
     * The attribute names that should never be recorded for this model.
     *
     * @return array<string>
     */
    public function getAuditExcludes(): array
    {
        $defaults = [$this->getKeyName(), 'created_at', 'updated_at', 'deleted_at'];

        $excluded = config('filament-audit-trail.exclude_attributes', []);

        $custom = method_exists($this, 'getAuditExcludeAttributes')
            ? $this->getAuditExcludeAttributes()
            : [];

        return array_values(array_unique(array_merge($defaults, $excluded, $custom)));
    }

    public function auditLogs(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\Zhenjun\AuditTrail\Models\AuditLog::class, 'auditable');
    }
}
