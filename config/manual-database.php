<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Manual Database Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration disables Laravel migrations and uses manual SQL imports
    | Set this to true when using manual database setup
    |
    */
    'manual_database_setup' => env('MANUAL_DATABASE_SETUP', false),
    
    /*
    |--------------------------------------------------------------------------
    | Skip Migration Check
    |--------------------------------------------------------------------------
    |
    | When enabled, Laravel won't check for pending migrations
    |
    */
    'skip_migration_check' => env('SKIP_MIGRATION_CHECK', false),
    
    /*
    |--------------------------------------------------------------------------
    | Database Files Location
    |--------------------------------------------------------------------------
    |
    | Location of SQL files for manual import
    |
    */
    'sql_files_path' => database_path('sql'),
    
    /*
    |--------------------------------------------------------------------------
    | Required SQL Files
    |--------------------------------------------------------------------------
    |
    | List of SQL files that should be imported in order
    |
    */
    'required_sql_files' => [
        'fresh_install.sql',  // For new installations
        'schema_mysql.sql',   // Full schema
        'data_mysql.sql',     // Sample data
    ],
];
