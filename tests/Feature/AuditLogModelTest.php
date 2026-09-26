<?php

use Zhenjun\AuditTrail\Models\AuditLog;

it('merges old and new values into an ordered changes list', function () {
    $log = new AuditLog([
        'old_values' => ['name' => 'A', 'email' => 'a@b.com'],
        'new_values' => ['name' => 'B', 'phone' => '123'],
    ]);

    expect($log->changes)->toBe([
        ['key' => 'name', 'old' => 'A', 'new' => 'B'],
        ['key' => 'email', 'old' => 'a@b.com', 'new' => null],
        ['key' => 'phone', 'old' => null, 'new' => '123'],
    ]);
});

it('has no updated_at column (append-only)', function () {
    expect(AuditLog::UPDATED_AT)->toBeNull();
});
