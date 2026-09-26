<?php

namespace Zhenjun\AuditTrail\Support\Defaults;

use Zhenjun\AuditTrail\Contracts\AuditHasher;

class NullAuditHasher implements AuditHasher
{
    public function hash(array $attributes, ?string $previousHash): ?string
    {
        return null;
    }
}
