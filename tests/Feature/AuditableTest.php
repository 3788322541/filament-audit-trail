<?php

use Zhenjun\AuditTrail\Models\AuditLog;
use Zhenjun\AuditTrail\Tests\Models\Post;
use Zhenjun\AuditTrail\Tests\Models\Widget;

it('records a created snapshot without excluded attributes', function () {
    $post = Post::create(['name' => 'Hello', 'email' => 'a@b.com', 'password' => 'secret']);

    $log = AuditLog::forRecord(Post::class, $post->getKey())->event('created')->first();

    expect($log)->not->toBeNull()
        ->and($log->new_values)->toHaveKey('name', 'Hello')
        ->and($log->new_values)->toHaveKey('email', 'a@b.com')
        ->and($log->new_values)->not->toHaveKey('password')
        ->and($log->old_values)->toBe([])
        ->and($log->auditable_name)->toBe('Hello');
});

it('records a field-level diff on update', function () {
    $post = Post::create(['name' => 'Before', 'email' => 'x@y.com']);

    $post->update(['name' => 'After']);

    $log = AuditLog::forRecord(Post::class, $post->getKey())->event('updated')->first();

    expect($log->old_values)->toBe(['name' => 'Before'])
        ->and($log->new_values)->toBe(['name' => 'After']);
});

it('skips an update that only touches excluded attributes', function () {
    $post = Post::create(['name' => 'N']);

    $post->update(['password' => 'new-secret']);

    expect(AuditLog::forRecord(Post::class, $post->getKey())->event('updated')->exists())->toBeFalse();
});

it('records a delete snapshot in old_values', function () {
    $post = Post::create(['name' => 'Doomed']);

    $post->delete();

    $log = AuditLog::forRecord(Post::class, $post->getKey())->event('deleted')->first();

    expect($log)->not->toBeNull()
        ->and($log->old_values)->toHaveKey('name', 'Doomed')
        ->and($log->new_values)->toBe([]);
});

it('records a restore on a soft-deleting model', function () {
    $post = Post::create(['name' => 'R']);
    $post->delete();

    $post->restore();

    expect(AuditLog::forRecord(Post::class, $post->getKey())->event('restored')->exists())->toBeTrue();
});

it('works on a model without soft deletes', function () {
    $widget = Widget::create(['name' => 'W']);
    $widget->update(['name' => 'W2']);
    $widget->delete();

    expect(AuditLog::forRecord(Widget::class, $widget->getKey())->orderBy('id')->pluck('event')->all())
        ->toBe(['created', 'updated', 'deleted']);
});
