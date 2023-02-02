<?php

namespace app\components\interfaces;

interface YesOrNoInterface
{
    public const YESORNO_NO = 'no';
    public const YESORNO_YES = 'yes';

    public const YESORNO_MAP = [
        self::YESORNO_NO => 'Нет',
        self::YESORNO_YES => 'Да',
    ];

}
