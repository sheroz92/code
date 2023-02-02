<?php

namespace app\components\interfaces;

interface GenderInterface
{
    public const GENDER_UNKNOWN = 'unknown';
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    public const GENDER_MAP = [
        self::GENDER_UNKNOWN => 'Неизвестно',
        self::GENDER_MALE => 'Мужской',
        self::GENDER_FEMALE => 'Женский',
    ];

}
