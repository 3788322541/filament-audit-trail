<?php

return [

    'single' => 'audit log',
    'plural' => 'audit logs',

    'navigation' => [
        'label' => 'Audit Logs',
        'group' => 'System',
    ],

    'events' => [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'restored' => 'Restored',
        'attached' => 'Attached',
        'detached' => 'Detached',
    ],

    'fields' => [
        'auditable' => 'Record',
        'auditable_name' => 'Record name',
        'event' => 'Action',
        'actor' => 'Performed by',
        'changes' => 'Changes',
        'created_at' => 'Date',
        'ip' => 'IP address',
        'url' => 'URL',
    ],

    'pages' => [
        'list' => [
            'title' => 'Audit Logs',
        ],
        'view' => [
            'title' => 'Audit Log :record',
        ],
    ],

    'diff' => [
        'old' => 'Old value',
        'new' => 'New value',
        'no_changes' => 'No field changes were recorded.',
    ],

];
