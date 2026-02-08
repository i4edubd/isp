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
    | nodes
    |--------------------------------------------------------------------------
    | The app can have multiple nodes but at least one node.
    | Comma Separated Values
    |
    */

    'nodes' => env('NODES', 'node1'),


    /*
    |--------------------------------------------------------------------------
    | Central Database Connection Name
    |--------------------------------------------------------------------------
    | The app requires a central database connection
    |
    */

    'central' => env('CENTRAL_DB_CONNECTION', 'central'),


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
            'url' => null,
            'database' => database_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'public_ip' => env('DB_PUBLIC_IP', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'dump' => [
                'do_not_create_tables',
                'timeout' => 60 * 5,
                'exclude_tables' => [
                    'all_customers', 'backup_settings', 'cache', 'customer_change_logs', 'migrations', 'failed_jobs', 'jobs', 'mikrotik_hotspot_user_profiles',
                    'mikrotik_ip_pools', 'mikrotik_ppp_profiles', 'mikrotik_ppp_secrets', 'nas_pppoe_profile', 'password_resets', 'radaccts', 'extend_package_validities',
                    'mikrotik_hotspot_users', 'radpostauths', 'sessions', 'sms_broadcast_jobs', 'sms_histories', 'temp_billing_profiles', 'temp_customers', 'deleted_customers',
                    'temp_packages', 'bulk_customer_bill_paids', 'customer_backup_requests', 'customer_bills_summaries', 'sales_comments', 'package_change_histories',
                    'customer_import_requests', 'customer_import_reports',
                ],
            ],
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'public_ip' => env('DB_PUBLIC_IP', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
            'dump' => [
                'do_not_create_tables',
                'timeout' => 60 * 5,
                'exclude_tables' => [
                    'bills_vs_payments_charts', 'pgsql_activity_logs', 'pgsql_customers', 'pgsql_radacct_histories',
                    'pgsql_radaccts', 'pgsql_radchecks', 'pgsql_radpostauths', 'pgsql_radreplies', 'pgsql_radusergroups',
                ],
            ],
        ],

        'infodb' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'public_ip' => env('DB_PUBLIC_IP', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => 'information_schema',
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'central' => [
            'driver' => 'mysql',
            'host' => env('CENTRAL_DB_HOST', '127.0.0.1'),
            'public_ip' => env('CENTRAL_DB_PUBLIC_IP', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'centralpgsql' => [
            'driver' => 'pgsql',
            'host' => env('CENTRAL_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ],

        'import_master' => [
            'driver' => 'mysql',
            'host' => env('IMPORT_MASTER_HOST', '127.0.0.1'),
            'port' => env('IMPORT_MASTER_DB_PORT', '6612'),
            'database' => env('IMPORT_MASTER_DB_DATABASE', 'forge'),
            'username' => env('IMPORT_MASTER_DB_USERNAME', 'forge'),
            'password' => env('IMPORT_MASTER_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'import_node' => [
            'driver' => 'mysql',
            'host' => env('IMPORT_NODE_HOST', '127.0.0.1'),
            'port' => env('IMPORT_NODE_DB_PORT', '6612'),
            'database' => env('IMPORT_NODE_DB_DATABASE', 'forge'),
            'username' => env('IMPORT_NODE_DB_USERNAME', 'forge'),
            'password' => env('IMPORT_NODE_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node1' => [
            'driver' => 'mysql',
            'host' => env('NODE1_DB_HOST', '127.0.0.1'),
            'public_ip' => env('NODE1_PUBLIC_IP', env('NODE1_DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', '6612'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node1pgsql' => [
            'driver' => 'pgsql',
            'host' => env('NODE1_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '6864'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ],

        'node2' => [
            'driver' => 'mysql',
            'host' => env('NODE2_DB_HOST', '127.0.0.1'),
            'public_ip' => env('NODE2_PUBLIC_IP', env('NODE2_DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', '6612'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node2pgsql' => [
            'driver' => 'pgsql',
            'host' => env('NODE2_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '6864'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ],

        'node3' => [
            'driver' => 'mysql',
            'host' => env('NODE3_DB_HOST', '127.0.0.1'),
            'public_ip' => env('NODE3_PUBLIC_IP', env('NODE3_DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', '6612'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node3pgsql' => [
            'driver' => 'pgsql',
            'host' => env('NODE3_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '6864'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ],

        'node4' => [
            'driver' => 'mysql',
            'host' => env('NODE4_DB_HOST', '127.0.0.1'),
            'public_ip' => env('NODE4_PUBLIC_IP', env('NODE4_DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', '6612'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node4pgsql' => [
            'driver' => 'pgsql',
            'host' => env('NODE4_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '6864'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ],

        'node5' => [
            'driver' => 'mysql',
            'host' => env('NODE5_DB_HOST', '127.0.0.1'),
            'public_ip' => env('NODE5_PUBLIC_IP', env('NODE5_DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', '6612'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node5pgsql' => [
            'driver' => 'pgsql',
            'host' => env('NODE5_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '6864'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ],

        'node6' => [
            'driver' => 'mysql',
            'host' => env('NODE6_DB_HOST', '127.0.0.1'),
            'public_ip' => env('NODE6_PUBLIC_IP', env('NODE6_DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', '6612'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node6pgsql' => [
            'driver' => 'pgsql',
            'host' => env('NODE6_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '6864'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ],

        'node7' => [
            'driver' => 'mysql',
            'host' => env('NODE7_DB_HOST', '127.0.0.1'),
            'public_ip' => env('NODE7_PUBLIC_IP', env('NODE7_DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', '6612'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node7pgsql' => [
            'driver' => 'pgsql',
            'host' => env('NODE7_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '6864'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ],

        'node8' => [
            'driver' => 'mysql',
            'host' => env('NODE8_DB_HOST', '127.0.0.1'),
            'public_ip' => env('NODE8_PUBLIC_IP', env('NODE8_DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', '6612'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node8pgsql' => [
            'driver' => 'pgsql',
            'host' => env('NODE8_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '6864'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ],

        'node9' => [
            'driver' => 'mysql',
            'host' => env('NODE9_DB_HOST', '127.0.0.1'),
            'public_ip' => env('NODE9_PUBLIC_IP', env('NODE9_DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', '6612'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node9pgsql' => [
            'driver' => 'pgsql',
            'host' => env('NODE9_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '6864'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
        ],

        'node10' => [
            'driver' => 'mysql',
            'host' => env('NODE10_DB_HOST', '127.0.0.1'),
            'public_ip' => env('NODE10_PUBLIC_IP', env('NODE10_DB_HOST', '127.0.0.1')),
            'port' => env('DB_PORT', '6612'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'node10pgsql' => [
            'driver' => 'pgsql',
            'host' => env('NODE10_DB_HOST', '127.0.0.1'),
            'port' => env('PGSQL_DB_PORT', '6864'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'disable',
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
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
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
