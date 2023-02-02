<?php

use yii\db\Connection;

$host = env('MYSQL_HOST', 'db');
$database = env('MYSQL_DATABASE', 'database');
$user = env('MYSQL_USER', 'user');
$password = env('MYSQL_PASSWORD', 'password');

return [
    'class' => Connection::class,
    'dsn' => "mysql:host={$host};dbname={$database}",
    'username' => $user,
    'password' => $password,
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
