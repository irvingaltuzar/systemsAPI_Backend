<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3507'),
            'database' => env('DB_DATABASE', 'intradb'),
            'username' => env('DB_USERNAME', 'desarrollo'),
            'password' => env('DB_PASSWORD', 'Irving1995'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'mysql2' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE2', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                PDO::MYSQL_ATTR_LOCAL_INFILE=>true,
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

		'erp_sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('ERP_HOST', 'localhost'),
            'port' => env('ERP_PORT', '1433'),
            'database' => env('ERP_DATABASE', 'forge'),
            'username' => env('ERP_USERNAME', 'forge'),
            'password' => env('ERP_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
			'Encrypt' => false,
			'TrustServerCertificate' => true,
            'prefix_indexes' => true,
			'pooling'  => false,
        ],

        'erp_dmi_sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('ERP_DMI_HOST', 'localhost'),
            'port' => env('ERP_DMI_PORT', '1433'),
            'database' => env('ERP_DMI_DATABASE', 'forge'),
            'charset' => 'utf8',
            'prefix' => '',
			'Encrypt' => false,
			'TrustServerCertificate' => true,
            'prefix_indexes' => true,
			'pooling'  => false,
            'options' => [
                'Authentication' => 'ActiveDirectoryPassword',
                'UID' => env('DB_USERNAME', ''),
                'PWD' => env('DB_PASSWORD', ''),
            ],
        ],
        'erp_6000_pruebas' => [
            'driver' => 'sqlsrv',
            'host' => env('ERP_6000P_HOST', 'localhost'),
            'port' => env('ERP_6000P_PORT', '1433'),
            'database' => env('ERP_6000P_DATABASE', 'forge'),
            'charset' => 'utf8',
            'prefix' => '',
			'Encrypt' => false,
			'TrustServerCertificate' => true,
            'trusted_connection' => 'yes', // Agrega esto
            'prefix_indexes' => true,
			'pooling'  => false,
            'options' => [
                'Authentication' => 'ActiveDirectoryPassword',
                'UID' => env('ERP_6000P_USERNAME', ''),
                'PWD' => env('ERP_6000P_PASSWORD', ''),
            ],
        ],
		'alfa_sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('INTRANET_HOST', 'localhost'),
            'port' => env('INTRANET_PORT', '1433'),
            'database' => env('INTRANET_DATABASE', 'forge'),
            'username' => env('INTRANET_USERNAME', 'forge'),
            'password' => env('INTRANET_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
			'Encrypt' => false,
			'TrustServerCertificate' => true,
            'prefix_indexes' => true,
			'pooling'  => false,
        ],

		'intranet_sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('INTRANET_HOST', 'localhost'),
            'port' => env('INTRANET_PORT', '1433'),
            'database' => env('INTRANET_DATABASE', 'forge'),
            'username' => env('INTRANET_USERNAME', 'forge'),
            'password' => env('INTRANET_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
			'Encrypt' => false,
			'TrustServerCertificate' => true,
            'prefix_indexes' => true,
			'pooling'  => false,
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
			'Encrypt' => false
        ],

        'attendance_sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('ATTENDANCE_HOST', 'localhost'),
            'port' => env('ATTENDANCE_PORT', '1433'),
            'database' => env('ATTENDANCE_DATABASE', 'forge'),
            'username' => env('ATTENDANCE_USERNAME', 'forge'),
            'password' => env('ATTENDANCE_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
			'Encrypt' => false,
			'TrustServerCertificate' => true,
            'prefix_indexes' => true,
			'pooling'  => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
