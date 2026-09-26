<?php

use Illuminate\Support\Carbon;
use Zhenjun\AuditTrail\Models\AuditLog;

function seedAuditLog(Carbon $createdAt): AuditLog
{
    $log = AuditLog::create([
        'auditable_type' => 'App\\Test',
        'auditable_id' => '1',
        'event' => 'created',
        'old_values' => [],
        'new_values' => [],
    ]);

    $log->forceFill(['created_at' => $createdAt])->save();

    return $log;
}

it('rejects a purge of fewer than one day', function () {
    seedAuditLog(Carbon::now());

    $this->artisan('audit:purge', ['--days' => 0])->assertExitCode(1);

    expect(AuditLog::count())->toBe(1);
});

it('deletes only logs older than the cutoff', function () {
    $old = seedAuditLog(Carbon::now()->subDays(400));
    $recent = seedAuditLog(Carbon::now()->subDays(10));

    $this->artisan('audit:purge', ['--days' => 30])->assertExitCode(0);

    expect(AuditLog::pluck('id')->all())->toBe([$recent->id])
        ->and(AuditLog::find($old->id))->toBeNull();
});
