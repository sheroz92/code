<?php

namespace app\modules\users\models;

use app\components\traits\FirstOrCreate;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 *
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $template_id [integer]
 * @property string $field [varchar(255)]
 * @property string $attribute [varchar(255)]
 * @property-read ActiveQuery $template
 * @property string $value [varchar(255)]
 */
class UserTemplateColumn extends ActiveRecord
{
    use FirstOrCreate;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%user_template_column}}';
    }

    /**
     * @return array[]
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return gmdate("Y-m-d H:i:s");
                },
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['field', 'attribute', 'value', 'created_at', 'updated_at'], 'trim'],
            [['field', 'attribute', 'value', 'created_at', 'updated_at'], 'string'],
            [['created_at', 'updated_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['id', 'template_id'], 'integer'],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserTemplate::class, 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * Gets query for [[UserTemplate]].
     *
     * @return ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(UserTemplate::class, ['id' => 'template_id']);
    }
}
