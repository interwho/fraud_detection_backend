<?php
define('PATH', realpath(__DIR__ . '/..'));

// Set Timezone
date_default_timezone_set('America/Toronto');

// Database
$GLOBALS['DATABASE'] = array(
    'dbname' => 'fraud_detection',
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'user' => 'root',
    'password' => 'password',
);
