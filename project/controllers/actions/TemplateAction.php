<?php

namespace app\controllers\actions;

use app\modules\users\models\UserTemplate;
use app\modules\users\models\UserTemplateColumn;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

/**
 * Class OptionsAction
 * @package api\controllers\actions
 */
class TemplateAction extends OptionsAction
{
    /**
     * @return string|void
     * @throws InvalidConfigException
     */
    public function run()
    {
        $entity = $this->entity;
        $request = Yii::$app->request->getBodyParams();
        $properties = $this->modelClass::entityFields();
        if (empty($request)) {
            $request = Yii::$app->request->getQueryParams();
        }
        $template = null;
        if (ArrayHelper::keyExists('use_template', $request) && $request['use_template'] && $use_template = UserTemplate::findOne($request['use_template'])) {
            UserTemplate::updateAll(['status' => 0], ['user_id' => Yii::$app->user->id]);
            $use_template->status = 1;
            $use_template->save();
        }
        if (ArrayHelper::keyExists('change_template', $request) && $request['change_template']) {
            $id = is_array($request['change_template']) && ArrayHelper::keyExists('id', $request['change_template']) && $request['change_template']['id'] ? $request['change_template']['id'] : 0;
            $template = UserTemplate::findOne($id);
            if (!$template && $request['change_template']['id'] == null) {
                UserTemplate::updateAll(['status' => 0], ['user_id' => Yii::$app->user->id]);
                $ut = new UserTemplate();
                $ut->user_id = Yii::$app->user->id;
                $ut->entity = $entity;
                $ut->status = 1;
                $ut->save();
                $template = $ut;
            }
            if ($template) {
                $template->name = strlen($request['change_template']['name']) ? $request['change_template']['name'] : 'Моя настройка';
                $template->save();
            }
            if (count($request['change_template']['visible']) && $template) {
                UserTemplateColumn::updateAll(['value' => 'false'], ['template_id' => $template->id]);
                foreach ($properties as $field => $fieldData) {
                    /* @var $ufc UserTemplateColumn */
                    $ufc = UserTemplateColumn::firstOrCreate(['template_id' => $template->id, 'field' => $field, 'attribute' => 'visible']);
                    if (ArrayHelper::isIn($field, $request['change_template']['visible'])) {
                        $ufc->value = 'true';
                        $ufc->save();
                    }else{
                        $ufc->value = 'false';
                        $ufc->save();
                    }
                }
            }
        }
        return $this->getProperties();
    }
}