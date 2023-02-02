<?php

namespace app\components;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\Controller as BaseApiController;
use yii\rest\Serializer;
use yii\web\Response;

/**
 * Class Controller
 * @package api\components
 */
class Controller extends BaseApiController
{
    private $_verbs = ['GET','POST','PATCH','PUT','DELETE', 'OPTIONS'];

    /**
     * @var string[]
     */
    public $serializer = [
        'class' => Serializer::class,
        'collectionEnvelope' => 'items',
    ];

    /**
     * @return array|array[]
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class
            ],
            //'except' => ['options', 'login']
        ];

        /* $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
         $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
 */
        return $behaviors;
    }
}