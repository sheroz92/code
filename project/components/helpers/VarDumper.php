<?php

namespace app\components\helpers;

class VarDumper extends \yii\helpers\VarDumper
{
    public static function vd($var, $exit = true): bool
    {
        parent::dump($var, 10, true);
        if ($exit) {
            exit;
        }
        return true;
    }
}
