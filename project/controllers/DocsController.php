<?php

namespace app\controllers;

use app\components\helpers\VarDumper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\OptionsAction;
use yii\web\Response;

/**
 * Class DocsController
 * @package api\controllers
 */
class DocsController extends \yii\web\Controller
{
    /**
     * @return array
     */
    public function actions(): array
    {
        return [
            'options' => [
                'class' => OptionsAction::class,
            ],
        ];
    }

    public function actionApi()
    {
        Yii::$app->response->format = Response::FORMAT_HTML;
        $url = Yii::$app->urlManager->createUrl(['docs/json']);
        return $this->render('index', [
            'url' => $url
        ]);
    }

    public function actionJson()
    {
        $json = Json::decode(file_get_contents(__DIR__ . '/../docs/api.json'));
        $modules = require __DIR__.'/../config/modules.php';
        foreach ($modules as $mod => $module) {
            if (ArrayHelper::keyExists('class', $module) && method_exists($module['class'], 'getApi')) {
                $class = $module['class'];
                $json = ArrayHelper::merge($json, Json::decode($class::getApi()));
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

}
