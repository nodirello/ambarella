<?php

declare(strict_types=1);

return [

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    | Absolute path (not realpath) so it works identically on shared hosting,
    | containerised servers and dev runtimes.
    */
    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),

];
