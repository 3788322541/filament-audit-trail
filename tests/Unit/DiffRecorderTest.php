<?php

use Illuminate\Support\Carbon;
use Zhenjun\AuditTrail\Support\DiffRecorder;
use Zhenjun\AuditTrail\Tests\Enums\PostStatus;

it('serializes a backed enum to its value', function () {
    expect(DiffRecorder::serialize(PostStatus::Published))->toBe('published');
});

it('serializes a date to its database string', function () {
    $date = new Carbon('2026-01-02 03:04:05');

    expect(DiffRecorder::serialize($date))->toBe('2026-01-02 03:04:05');
});

it('leaves scalars untouched', function () {
    expect(DiffRecorder::serialize('string'))->toBe('string')
        ->and(DiffRecorder::serialize(5))->toBe(5)
        ->and(DiffRecorder::serialize(null))->toBeNull();
});

it('normalises arrays through json', function () {
    expect(DiffRecorder::serialize(['a' => 1, 'b' => [2]]))->toBe(['a' => 1, 'b' => [2]]);
});
