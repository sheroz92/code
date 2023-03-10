<?php

namespace app\modules\imports\models;

use app\modules\imports\components\behaviors\ImportBehavior;
use app\modules\users\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $user_id [integer]
 * @property string $entity [varchar(255)]
 * @property string $file [varchar(255)]
 * @property int $completed_at [timestamp(0)]
 * @property-read ActiveQuery $user
 * @property string $percent [integer]
 */
class Import extends ActiveRecord
{

    public const STATUS_WAITING = 1;
    public const STATUS_PROGRESS = 2;
    public const STATUS_COMPLETED = 3;
    public const STATUS_ERROR = 4;

    public const STATUS_MAP = [
        self::STATUS_WAITING => 'Ожидает',
        self::STATUS_PROGRESS => 'Выполняется',
        self::STATUS_COMPLETED => 'Выполнен',
        self::STATUS_ERROR => 'Ошибка',
    ];

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%import}}';
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
            'import' => [
                'class' => ImportBehavior::class
            ]
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['filter'], $fields['updated_at']);
        return $fields;
    }

    public function beforeValidate()
    {
        if (!$this->user_id) {
            //$this->user_id = Yii::$app->user->id;
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['file'], 'trim'],
            [['file'], 'string'],
            [['entity', 'file'], 'required', 'on' => ['create', 'update']],
            [['id', 'status'], 'safe'],
            [['id'], 'integer'],
            [['status'], 'integer', 'min' => 1, 'max' => 100],
            [['completed_at', 'created_at', 'updated_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'status' => 'Статус',
            'entity' => 'Сущность',
            'file' => 'Файл',
            'percent' => 'Процент',
            'completed_at' => 'Дата заверщения',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }

    public static function entityFields()
    {
        $attributeLabels = (new self())->attributeLabels();
        return [
            "id" => [
                "title" => "ID",
                "type" => "integer",
                "format" => "int64",
                "readOnly" => true,
                "creatable" => false,
                "updatable" => false
            ],
            "status" => [
                "title" => $attributeLabels['status'],
                "type" => "string",
                "creatable" => true,
                "updatable" => true
            ],
            "entity" => [
                "title" => $attributeLabels['entity'],
                "type" => "string",
                "creatable" => true,
                "updatable" => true
            ],
            "file" => [
                "title" => $attributeLabels['file'],
                "type" => "string",
                "creatable" => true,
                "updatable" => true
            ],
            "percent" => [
                "title" => $attributeLabels['percent'],
                "type" => "integer",
                "creatable" => true,
                "updatable" => true
            ],
            "user_id" => [
                "title" => $attributeLabels['user_id'],
                "type" => "integer",
                "creatable" => true,
                "updatable" => true
            ],
            "completed_at" => [
                "title" => $attributeLabels['completed_at'],
                "type" => "string",
                "format" => "date-time",
                "readOnly" => true,
                "creatable" => false,
                "updatable" => false
            ],
            "created_at" => [
                "title" => $attributeLabels['created_at'],
                "type" => "string",
                "format" => "date-time",
                "readOnly" => true,
                "creatable" => false,
                "updatable" => false
            ],
            "updated_at" => [
                "title" => $attributeLabels['updated_at'],
                "type" => "string",
                "format" => "date-time",
                "readOnly" => true,
                "creatable" => false,
                "updatable" => false
            ]
        ];
    }

}
