<?php

use Zhenjun\AuditTrail\Models\AuditLog;
use Zhenjun\AuditTrail\Tests\Models\Team;

it('resolves the team relationship from the team_model config key', function () {
    config()->set('filament-audit-trail.team_model', Team::class);

    $team = Team::create(['name' => 'Acme']);

    $log = AuditLog::create([
        'auditable_type' => 'post',
        'auditable_id' => '1',
        'event' => 'created',
        'team_id' => $team->id,
    ]);

    expect($log->team)->toBeInstanceOf(Team::class)
        ->and($log->team->name)->toBe('Acme')
        ->and($log->team->is($team))->toBeTrue();
});

it('returns null from the team relationship when team_id is null', function () {
    config()->set('filament-audit-trail.team_model', Team::class);

    $log = AuditLog::create([
        'auditable_type' => 'post',
        'auditable_id' => '1',
        'event' => 'created',
        'team_id' => null,
    ]);

    expect($log->team)->toBeNull();
});

it('reports a null team model when nothing is configured', function () {
    expect(AuditLog::teamModel())->toBeNull();
});

it('throws when the team relationship is accessed without a team model', function () {
    expect(fn() => (new AuditLog)->team())
        ->toThrow(LogicException::class);
});
