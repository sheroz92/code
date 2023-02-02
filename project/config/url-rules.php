<?php

return [
    'GET,OPTIONS docs/<action:[\w-]+>/<module:[\w-]+>' => 'docs/<action>',

    'GET <controller:[\w-]+>' => '<controller>/index',
    'GET <controller:[\w-]+>/<id:\d+>' => '<controller>/view',
    'OPTIONS <controller:[\w-]+>' => '<controller>/options',
    'OPTIONS <controller:[\w-]+>/<id:\d+>' => '<controller>/options',
    'POST <controller:[\w-]+>' => '<controller>/create',
    'PUT <controller:[\w-]+>/<id:\d+>' => '<controller>/update',
    'DELETE <controller:[\w-]+>/<id:\d+>' => '<controller>/delete',

    '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
    '<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<controller>/<action>',

    'GET <module:[\w-]+>/<controller:[\w-]+>' => '<module>/<controller>/index',
    'GET <module:[\w-]+>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/view',
    'OPTIONS <module:[\w-]+>/<controller:[\w-]+>' => '<module>/<controller>/options',
    'OPTIONS <module:[\w-]+>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/options',
    'POST <module:[\w-]+>/<controller:[\w-]+>' => '<module>/<controller>/create',
    'PUT <module:[\w-]+>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/update',
    'DELETE <module:[\w-]+>/<controller:[\w-]+>/<id:\d+>' => '<module>/<controller>/delete',

    '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>' => '<module>/<controller>/<action>',
    '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<module>/<controller>/<action>',
];
