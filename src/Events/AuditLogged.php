<?php

namespace Zhenjun\AuditTrail\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Zhenjun\AuditTrail\Models\AuditLog;

class AuditLogged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public AuditLog $auditLog,
    ) {}
}
