<?php

/**
 * Autoload the HubEntitlement SDK
 *
 * @author jsunico@cambridge.org
 */


if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    throw new Exception(
        'The HubEntitlement SDK requires PHP version 5.4 or higher.'
    );
}

if (!file_exists(dirname(__FILE__) . '/.env')) {
    throw new Exception(
        'The HubEntitlement SDK requires .env file in SDK\'s root directory.'
    );
}

require_once dirname(__FILE__) . '/vendor/autoload.php';

use HubEntitlement\Configuration\ConfigurationManager;
use Illuminate\Database\Capsule\Manager as Database;

$configStore = new ConfigurationManager(__DIR__);

$capsule = new Database;
$capsule->addConnection([
    'driver' => getenv('DB_CONNECTION'),
    'host' => getenv('DB_HOST'),
    'database' => getenv('DB_NAME'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
