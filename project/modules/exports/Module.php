<?php

namespace app\modules\exports;

use app\modules\exports\models\Export;

/**
 * exports module definition class
 *
 * @property-read array|string[] $models
 * @property-read string $api
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\exports\controllers';

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
            Export::class,
        ];
    }
}
