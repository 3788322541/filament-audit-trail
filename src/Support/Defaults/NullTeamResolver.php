<?php

namespace Zhenjun\AuditTrail\Support\Defaults;

use Illuminate\Database\Eloquent\Model;
use Zhenjun\AuditTrail\Contracts\TeamResolver;

class NullTeamResolver implements TeamResolver
{
    public function resolve(Model $auditable, ?Model $actor): int|string|null
    {
        return null;
    }
}
