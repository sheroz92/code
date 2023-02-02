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
 * @property string $user_id [integer]
 * @property string $status [integer]
 * @property string $entity [varchar(255)]
 * @property string $name [varchar(255)]
 * @property-read ActiveQuery $user
 */
class UserTemplate extends ActiveRecord
{
    use FirstOrCreate;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%user_template}}';
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
            [['entity', 'name', 'created_at', 'updated_at'], 'trim'],
            [['entity', 'name', 'created_at', 'updated_at'], 'string'],
            [['created_at', 'updated_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['id', 'user_id', 'status'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
