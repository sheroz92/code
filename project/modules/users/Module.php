<?php

namespace app\modules\users;

use app\modules\users\models\User;

/**
 * users module definition class
 *
 * @property-read string $api
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\users\controllers';

    /**
     * {@inheritdoc}
     */
    public static function getApi(): string
    {
        return file_get_contents(__DIR__.'/docs/api.json');
    }

    /**
     * @return string[]
     */
    public function getModels(): array
    {
        return [
            User::class,
        ];
    }
}
