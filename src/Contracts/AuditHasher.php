<?php

namespace Zhenjun\AuditTrail\Contracts;

interface AuditHasher
{
    /**
     * Compute the tamper-evident hash for a log entry about to be persisted.
     *
     * The free tier binds a null implementation that always returns null, so
     * the `audit_logs.hash` column stays empty until the Pro package is
     * installed and enables hashing via `config('filament-audit-trail.pro.hashing.enabled')`.
     *
     * @param  array<string, mixed>  $attributes  The log attributes prior to insertion.
     * @param  string|null  $previousHash  The hash of the immediately preceding entry, if any.
     */
    public function hash(array $attributes, ?string $previousHash): ?string;
}
