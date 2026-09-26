<?php

namespace Zhenjun\AuditTrail\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Throwable;

class AuditContext
{
    protected static ?Authenticatable $actor = null;

    protected static bool $isSilent = false;

    protected static array $tags = [];

    /**
     * Run a callback without recording any audit logs.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public static function silent(callable $callback): mixed
    {
        $previous = static::$isSilent;

        static::$isSilent = true;

        try {
            return $callback();
        } finally {
            static::$isSilent = $previous;
        }
    }

    /**
     * Run a callback attributing all recorded changes to the given actor.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public static function for(?Authenticatable $actor, callable $callback): mixed
    {
        $previous = static::$actor;

        static::$actor = $actor;

        try {
            return $callback();
        } finally {
            static::$actor = $previous;
        }
    }

    public static function isSilent(): bool
    {
        return static::$isSilent;
    }

    /**
     * Add tags that will be stored with every log written until the request ends.
     *
     * @param  string|array<string>  $tags
     */
    public static function tag(string|array $tags): void
    {
        foreach ((array) $tags as $tag) {
            static::$tags[] = $tag;
        }
    }

    /**
     * @return array<string>
     */
    public static function tags(): array
    {
        return array_values(array_unique(static::$tags));
    }

    /**
     * Resolve the current actor: explicit override, then the Filament panel
     * user, then the default Laravel guard.
     */
    public static function resolveActor(): ?Authenticatable
    {
        if (static::$actor !== null) {
            return static::$actor;
        }

        try {
            $user = filament()->auth()->getUser();
        } catch (Throwable) {
            $user = null;
        }

        if ($user instanceof Authenticatable) {
            return $user;
        }

        $guard = config('filament-audit-trail.auth_guard');

        return auth($guard ?: null)->user();
    }
}
