<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'photodemandeur' => [
            /*,
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),*/
            'driver'        => 'local',
            'root'          => storage_path('app/photodemandeur'),
            'url'           => env('APP_URL').'/photodemandeur',
            'visibility'    => 'public',
        ],
        'planaffaireprojet' => [
            'driver'        => 'local',
            'root'          => storage_path('app/planaffaireprojet'),
            'url'           => env('APP_URL').'/planaffaireprojet',
            'visibility'    => 'public',
        ],
        'photobackend' => [
            'driver'        => 'local',
            'root'          => storage_path('app/photobackend'),
            'url'           => env('APP_URL').'/photobackend',
            'visibility'    => 'public',
        ],
        'formationcni' => [
            'driver'        => 'local',
            'root'          => storage_path('app/formationcni'),
            'url'           => env('APP_URL').'/formationcni',
            'visibility'    => 'public',
        ],
        'formationdiplome' => [
            'driver'        => 'local',
            'root'          => storage_path('app/formationdiplome'),
            'url'           => env('APP_URL').'/formationdiplome',
            'visibility'    => 'public',
        ],
        'comitecertif_rapport' => [
            'driver'        => 'local',
            'root'          => storage_path('app/comitecertif_rapport'),
            'url'           => env('APP_URL').'/comitecertif_rapport',
            'visibility'    => 'public',
        ],'comitecertif_pv' => [
            'driver'        => 'local',
            'root'          => storage_path('app/comitecertif_pv'),
            'url'           => env('APP_URL').'/comitecertif_pv',
            'visibility'    => 'public',
        ],
        'diplomedemandeur' => [
            'driver'        => 'local',
            'root'          => storage_path('app/diplomedemandeur'),
            'url'           => env('APP_URL').'/diplomedemandeur',
            'visibility'    => 'public',
        ],
        'cnidemandeur' => [
            'driver'        => 'local',
            'root'          => storage_path('app/cnidemandeur'),
            'url'           => env('APP_URL').'/cnidemandeur',
            'visibility'    => 'public',
        ],
        'cvdemandeur' => [
            'driver'        => 'local',
            'root'          => storage_path('app/cvdemandeur'),
            'url'           => env('APP_URL').'/cvdemandeur',
            'visibility'    => 'public',
        ],
        'file_notificationaccord' => [
            'driver'        => 'local',
            'root'          => storage_path('app/file_notificationaccord'),
            'url'           => env('APP_URL').'/file_notificationaccord',
            'visibility'    => 'public',
        ],
        'file_actesnantissement' => [
            'driver'        => 'local',
            'root'          => storage_path('app/file_actesnantissement'),
            'url'           => env('APP_URL').'/file_actesnantissement',
            'visibility'    => 'public',
        ],
        'file_tableauamortissement' => [
            'driver'        => 'local',
            'root'          => storage_path('app/file_tableauamortissement'),
            'url'           => env('APP_URL').'/file_tableauamortissement',
            'visibility'    => 'public',
        ],
        'file_contratpret' => [
            'driver'        => 'local',
            'root'          => storage_path('app/file_contratpret'),
            'url'           => env('APP_URL').'/file_contratpret',
            'visibility'    => 'public',
        ],
        'file_actesgarantieadditionnel' => [
            'driver'        => 'local',
            'root'          => storage_path('app/file_actesgarantieadditionnel'),
            'url'           => env('APP_URL').'/file_actesgarantieadditionnel',
            'visibility'    => 'public',
        ],
        'file_plandecaissement' => [
            'driver'        => 'local',
            'root'          => storage_path('app/file_plandecaissement'),
            'url'           => env('APP_URL').'/file_plandecaissement',
            'visibility'    => 'public',
        ],
        'file_ficherecapitulative' => [
            'driver'        => 'local',
            'root'          => storage_path('app/file_ficherecapitulative'),
            'url'           => env('APP_URL').'/file_ficherecapitulative',
            'visibility'    => 'public',
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |cnidemandeur cvdemandeur
    */
    'links' => [
        public_path('formationcni')         => storage_path('app/formationcni'),
        public_path('formationdiplome')     => storage_path('app/formationdiplome'),
        public_path('comitecertif_pv')      => storage_path('app/comitecertif_pv'),
        public_path('comitecertif_rapport') => storage_path('app/comitecertif_rapport'),
        public_path('photodemandeur')       => storage_path('app/photodemandeur'),
        public_path('diplomedemandeur')     => storage_path('app/diplomedemandeur'),
        public_path('cnidemandeur')         => storage_path('app/cnidemandeur'),
        public_path('cvdemandeur')          => storage_path('app/cvdemandeur'),
        public_path('planaffaireprojet')    => storage_path('app/planaffaireprojet'),
        public_path('photobackend')         => storage_path('app/photobackend'),
        public_path('file_actesnantissement')           => storage_path('app/file_actesnantissement'),
        public_path('file_notificationaccord')          => storage_path('app/file_notificationaccord'),
        public_path('file_tableauamortissement')        => storage_path('app/file_tableauamortissement'),
        public_path('file_contratpret')                 => storage_path('app/file_contratpret'),
        public_path('file_actesgarantieadditionnel')    => storage_path('app/file_actesgarantieadditionnel'),
        public_path('file_plandecaissement')            => storage_path('app/file_plandecaissement'),
        public_path('file_ficherecapitulative')         => storage_path('app/file_ficherecapitulative'),
    ],

];
