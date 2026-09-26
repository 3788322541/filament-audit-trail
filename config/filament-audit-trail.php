<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | User model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of your Filament panel's user model.
    | This is used to render the actor column and relation in the UI.
    |
    */

    'user_model' => User::class,

    /*
    |--------------------------------------------------------------------------
    | User name column
    |--------------------------------------------------------------------------
    |
    | The column used to display the actor's name in the audit log UI.
    |
    */

    'user_name_column' => 'name',

    /*
    |--------------------------------------------------------------------------
    | Auth guard
    |--------------------------------------------------------------------------
    |
    | The guard used to resolve the actor when no Filament panel context is
    | available (e.g. queue workers, custom controllers). Null uses the
    | application default guard.
    |
    */

    'auth_guard' => null,

    /*
    |--------------------------------------------------------------------------
    | Excluded attributes
    |--------------------------------------------------------------------------
    |
    | Attributes that will never be recorded in audit logs, on every model.
    | Individual models may add more via getAuditExcludeAttributes().
    |
    */

    'exclude_attributes' => [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Display name column
    |--------------------------------------------------------------------------
    |
    | When auditing a record, this column (or accessor) is stored alongside the
    | log so the UI can identify the audited record even after it is deleted.
    | You may also implement getAuditRecordName(): ?string on your model to
    | override this per model.
    |
    */

    'record_name_column' => 'name',

    /*
    |--------------------------------------------------------------------------
    | Record request context
    |--------------------------------------------------------------------------
    |
    | Store the request URL, referer, IP address and user agent with each log.
    |
    */

    'record_url' => true,

    'record_ip' => true,

    'record_user_agent' => true,

    /*
    |--------------------------------------------------------------------------
    | Audit console changes
    |--------------------------------------------------------------------------
    |
    | By default, changes made while running in console (seeds, imports,
    | scheduled jobs) are still recorded, with a "system" actor and a
    | "console" tag. Set this to false to skip them entirely.
    |
    */

    'record_console' => true,

    /*
    |--------------------------------------------------------------------------
    | Purge
    |--------------------------------------------------------------------------
    |
    | Default number of days kept by the "audit:purge" command.
    |
    */

    'purge_days' => 365,

    /*
    |--------------------------------------------------------------------------
    | Pro tier
    |--------------------------------------------------------------------------
    |
    | Extension points consumed by zhenjun/filament-audit-trail-pro. The free
    | package ships null / pass-through implementations for the AuditHasher,
    | TeamResolver and FieldPolicy contracts, so leaving this section untouched
    | has zero effect on behaviour. Enabling any key here without the Pro
    | package installed is a no-op (the null bindings still return null).
    |
    */

    'pro' => [

        'hashing' => [
            // Compute and store `audit_logs.hash` as an HMAC-SHA256 chain when true.
            'enabled' => false,
            // Shared secret used by the Pro AuditHasher. Rotate via env, never commit.
            'secret' => env('AUDIT_TRAIL_HASH_SECRET'),
        ],

        'team' => [
            // Class name of a custom TeamResolver. Null uses the Pro package default
            // (Filament team context / current tenant).
            'resolver' => null,
        ],

        'fields' => [
            // Per-model whitelist. Empty = record everything the exclude list allows
            // (free behaviour). Populated = Pro enforces it.
            'whitelist' => [],
            // Attribute names whose stored values should be masked in the diff.
            'mask' => [],
        ],

    ],

];
