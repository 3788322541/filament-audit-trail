<?php

namespace Zhenjun\AuditTrail\Support\Defaults;

use Illuminate\Database\Eloquent\Model;
use Zhenjun\AuditTrail\Contracts\FieldPolicy;

class PassThroughFieldPolicy implements FieldPolicy
{
    public function apply(Model $model, string $event, array $diff): array
    {
        return $diff;
    }
}
