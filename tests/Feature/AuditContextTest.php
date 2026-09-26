<?php

use Zhenjun\AuditTrail\Models\AuditLog;
use Zhenjun\AuditTrail\Support\AuditContext;
use Zhenjun\AuditTrail\Tests\Models\Post;
use Zhenjun\AuditTrail\Tests\Models\User;

it('records nothing inside silent()', function () {
    AuditContext::silent(function () {
        Post::create(['name' => 'Quiet']);
    });

    expect(AuditLog::count())->toBe(0);
});

it('restores recording once the silent callback finishes', function () {
    AuditContext::silent(fn () => Post::create(['name' => 'Quiet']));

    Post::create(['name' => 'Loud']);

    expect(AuditLog::count())->toBe(1);
});

it('attributes changes to an explicit actor via for()', function () {
    $actor = new User(['id' => 42]);

    AuditContext::for($actor, function () {
        Post::create(['name' => 'Authored']);
    });

    $log = AuditLog::latest('id')->first();

    expect($log->actor_type)->toBe(User::class)
        ->and((int) $log->actor_id)->toBe(42);
});

it('accumulates unique tags', function () {
    AuditContext::tag('import');
    AuditContext::tag(['bulk', 'import']);

    expect(AuditContext::tags())->toBe(['import', 'bulk']);
});
