<?php

namespace app\modules\exports\controllers;

use app\components\Controller;
use app\controllers\actions\OptionsAction;
use app\controllers\actions\TemplateAction;
use app\controllers\actions\UpdateAction;
use app\modules\exports\models\Export;
use yii\data\ActiveDataFilter;
use yii\filters\AccessControl;
use yii\rest\CreateAction;
use yii\rest\DeleteAction;
use yii\rest\IndexAction;
use yii\rest\ViewAction;

class ManageController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors = array_merge($behaviors, [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['template', 'options','index', 'view', 'create', 'update', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
        return $behaviors;
    }

    public function actions(): array
    {
        return [
            'template' => [
                'class' => TemplateAction::class,
                'entity' => 'export',
                'modelClass' => Export::class,
            ],
            'options' => [
                'class' => OptionsAction::class,
                'entity' => 'export',
                'modelClass' => Export::class,
            ],
            'index' => [
                'class' => IndexAction::class,
                'modelClass' => Export::class,
                'dataFilter' => [
                    'class' => ActiveDataFilter::class,
                    'searchModel' => Export::class
                ],
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => CreateAction::class,
                'modelClass' => Export::class,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => 'create',
            ],
            'update' => [
                'class' => UpdateAction::class,
                'modelClass' => Export::class,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => 'create',
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'modelClass' => Export::class,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'view' => [
                'class' => ViewAction::class,
                'modelClass' => Export::class,
                'checkAccess' => [$this, 'checkAccess'],
            ]
        ];
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // TODO implement checkAccess
    }

}
