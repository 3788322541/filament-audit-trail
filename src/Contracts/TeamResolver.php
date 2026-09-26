<?php

namespace Zhenjun\AuditTrail\Contracts;

use Illuminate\Database\Eloquent\Model;

interface TeamResolver
{
    /**
     * Resolve the team / tenant id a change belongs to.
     *
     * The free tier binds a null implementation, so `audit_logs.team_id`
     * stays empty until the Pro package registers a resolver that reads the
     * active Filament team, stancl/tenancy current tenant, or any custom
     * multi-tenancy context.
     */
    public function resolve(Model $auditable, ?Model $actor): int|string|null;
}
