<?php

namespace Zhenjun\AuditTrail\Contracts;

use Illuminate\Database\Eloquent\Model;

interface FieldPolicy
{
    /**
     * Filter and/or transform the field-level diff before it is persisted.
     *
     * The free tier binds a pass-through implementation. The Pro package uses
     * this hook to enforce a per-model whitelist and to mask sensitive values
     * (e.g. card numbers, national IDs) before they hit storage.
     *
     * @param  array{old: array<string, mixed>, new: array<string, mixed>}  $diff
     * @return array{old: array<string, mixed>, new: array<string, mixed>}
     */
    public function apply(Model $model, string $event, array $diff): array;
}
