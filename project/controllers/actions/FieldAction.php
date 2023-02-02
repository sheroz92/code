<?php

namespace app\controllers\actions;

use app\modules\users\models\UserTemplate;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

/**
 * Class OptionsAction
 * @package api\controllers\actions
 */
class FieldAction extends OptionsAction
{
    /**
     * @return string|void
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     */
    public function run()
    {
        $entity = $this->entity;
        $request = Yii::$app->request->getBodyParams();
        if (empty($request)) {
            $request = Yii::$app->request->getQueryParams();
        }
        $fieldName = $request['fieldName'];
        $properties = $this->modelClass::entityFields();
        $attribute = $request['attribute'];
        $value = $request['value'];
        if (!ArrayHelper::keyExists($fieldName, $properties)) {
            throw new BadRequestHttpException('FieldName invalid');
        }
        /* @var $userColumn UserTemplate */
        $userColumn = UserTemplate::firstOrCreate(['user_id' => Yii::$app->user->id, 'entity' => $entity, 'field' => $fieldName, 'attribute' => $attribute]);
        $userColumn->value = $value;
        $userColumn->save();

        return $this->getProperties();
    }
}