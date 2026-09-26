<?php

namespace Zhenjun\AuditTrail\Support;

use BackedEnum;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DiffRecorder
{
    /**
     * Build the [old, new] diff for the attributes currently dirty on the model.
     *
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    public static function forUpdate(Model $model, array $exclude): array
    {
        $old = [];
        $new = [];

        foreach ($model->getDirty() as $key => $value) {
            if (in_array($key, $exclude, true)) {
                continue;
            }

            $old[$key] = static::serialize($model->getOriginal($key));
            $new[$key] = static::serialize($model->getAttributeValue($key));
        }

        return [$old, $new];
    }

    /**
     * Capture every present attribute as either the new state (created) or the
     * old state (deleted).
     *
     * @return array<string, mixed>
     */
    public static function snapshot(Model $model, array $exclude): array
    {
        $values = [];

        foreach ($model->getAttributes() as $key => $value) {
            if (in_array($key, $exclude, true)) {
                continue;
            }

            $values[$key] = static::serialize($model->getAttributeValue($key));
        }

        return $values;
    }

    /**
     * Normalise a raw attribute value into a JSON-friendly scalar.
     */
    public static function serialize(mixed $value): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if ($value instanceof DateTimeInterface || $value instanceof Carbon) {
            return $value->toDateTimeString();
        }

        if (is_array($value) || is_object($value)) {
            return json_decode((string) json_encode($value), true);
        }

        return $value;
    }
}
