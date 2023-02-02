<?php

namespace app\modules\users\controllers;

use app\components\Controller;
use app\controllers\actions\TemplateAction;
use app\controllers\actions\UpdateAction;
use app\modules\users\models\LoginForm;
use app\modules\users\models\User;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataFilter;
use yii\filters\AccessControl;
use yii\rest\CreateAction;
use yii\rest\DeleteAction;
use yii\rest\IndexAction;
use app\controllers\actions\OptionsAction;
use yii\rest\ViewAction;
use yii\web\BadRequestHttpException;

class ManageController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['login'];
        $behaviors = array_merge($behaviors, [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['template', 'options','index', 'view', 'create', 'update', 'delete', 'me', 'logout'],
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
                'entity' => 'user',
                'modelClass' => User::class,
            ],
            'options' => [
                'class' => OptionsAction::class,
                'entity' => 'user',
                'modelClass' => User::class,
            ],
            'index' => [
                'class' => IndexAction::class,
                'modelClass' => User::class,
                'dataFilter' => [
                    'class' => ActiveDataFilter::class,
                    'searchModel' => User::class
                ],
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => CreateAction::class,
                'modelClass' => User::class,
                'checkAccess' => [$this, 'checkAccess']
            ],
            'update' => [
                'class' => UpdateAction::class,
                'modelClass' => User::class,
                'checkAccess' => [$this, 'checkAccess']
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'modelClass' => User::class,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'view' => [
                'class' => ViewAction::class,
                'modelClass' => User::class,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'me'
        ];
    }

    /**
     * @return LoginForm|array
     * @throws Exception
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        if (!$model->load(Yii::$app->getRequest()->getQueryParams(), '')) {
            throw new BadRequestHttpException();
        }

        if ($model->load(Yii::$app->getRequest()->getQueryParams(), '') && $model->login()) {
            return $model->user->getUserInfo(['auth_key']);
        }
        return $model;
    }

    /**
     * @return array
     */
    public function actionLogout(): array
    {
        if (Yii::$app->user->identity) {
            Yii::$app->user->identity->auth_key = NULL;
            Yii::$app->user->identity->save(false);
        }
        return ['status' => 'ok'];
    }

    /**
     * @return mixed
     */
    public function actionMe()
    {
        //$this->checkAccess();
        $user = Yii::$app->user->identity;
        /**@var $user User */
        return $user->getUserInfo();
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // TODO implement checkAccess
    }

}
