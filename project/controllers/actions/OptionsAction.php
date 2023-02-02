<?php

namespace app\controllers\actions;

use app\modules\users\models\UserTemplate;
use app\modules\users\models\UserTemplateColumn;
use Yii;
use \yii\base\Action;
use yii\helpers\ArrayHelper;

/**
 * Class OptionsAction
 * @package api\controllers\actions
 *
 * @property-read mixed $properties
 */
class OptionsAction extends Action
{
    public $entity;
    public $modelClass;

    /**
     * @return string|void
     */
    public function run()
    {
        return $this->getProperties();
    }

    public function getProperties()
    {
        $entity = $this->entity;
        $properties = $this->modelClass::entityFields();
        $model = $this->modelClass;
        $template = UserTemplate::find()->where(['user_id' => Yii::$app->user->id, 'entity' => $entity, 'status' => 1])->one();
        if(!$template){
            $template = UserTemplate::find()->where(['user_id' => Yii::$app->user->id, 'entity' => $entity])->one();
        }
        $templates = UserTemplate::find()->where(['user_id' => Yii::$app->user->id, 'entity' => $entity])->all();

        foreach ($properties as $field => $fieldData) {
            if ($template) {
                $userColumns = UserTemplateColumn::findAll(['template_id' => $template->id, 'field' => $field]);
                foreach ($userColumns as $column) {
                    $value = $column->value === 'true' ? true : ($column->value === 'false' ? false : $column->value);
                    $properties[$field][$column->attribute] = $value;
                }
            }

            if (!ArrayHelper::keyExists('value', $fieldData) && $fieldData['type'] === 'array' && ArrayHelper::isIn($fieldData['format'], ['radio', 'select'])) {
                $map = strtoupper($field) . '_MAP';
                if (!ArrayHelper::keyExists('value', $properties[$field]) && is_array(constant("$model::$map"))) {
                    $properties[$field]['value'] = constant("$model::$map");
                }
            }
        }

        return [
            'template' => $template,
            'templates' => $templates,
            'fields' => $properties
        ];
    }
}