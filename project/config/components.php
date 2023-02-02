<?php

use Edvlerblog\Adldap2\Adldap2Wrapper;
use sizeg\jwt\Jwt;
use yii\mutex\MysqlMutex;
use yii\queue\db\Queue;
use yii\queue\LogBehavior;
use yii\rbac\DbManager;

return [
    'authManager' => [
        'class' => DbManager::class
    ],
    'jwt' => [
        'class' => Jwt::class,
        'key' => '34hbi#f39$438n^fwo3(wndjkldet8re3/2345H4n453$2dfnkjf45',
    ],
    'queue' => [
        'class' => Queue::class,
        'db' => 'db', // DB connection component or its config
        'tableName' => '{{%queue}}', // Table name
        'channel' => 'site', // Queue channel key
        'as log' => LogBehavior::class,
        'mutex' => MysqlMutex::class, // Mutex used to sync queries,
        'ttr' => (60 * 60) * 3, // Максимальное время выполнения задания 3 часа
        'attempts' => 3, // Максимальное кол-во попыток
    ],
];