<?php

namespace app\modules\users\models;

use app\components\traits\FirstOrCreate;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $login
 * @property string $auth_key
 * @property string|null $status
 * @property string $role
 * @property string $created_at
 * @property-read null|string $authKey
 * @property-read array $userInfo
 * @property string $updated_at
 * @property string $email [varchar(255)]
 * @property string $phone [varchar(255)]
 * @property string $first_name [varchar(255)]
 * @property string $last_name [varchar(255)]
 * @property string $password [varchar(255)]
 * @property string $password_hash
 */
class User extends ActiveRecord implements IdentityInterface
{
    use FirstOrCreate;

    public $password;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_NOACTIVE = 'noactive';

    public const STATUS_MAP = [
        self::STATUS_ACTIVE => 'Активный',
        self::STATUS_NOACTIVE => 'Не активный',
    ];

    public const ROLE_USER = 'user';
    public const ROLE_SUPERADMIN = 'superadmin';

    public function behaviors(): array
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
     * @return string
     */
    public static function tableName(): string
    {
        return 'user';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['id', 'integer'],
            [['login'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['login', 'role'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 1000],
            [['status'], 'string', 'max' => 20],
            [['first_name', 'last_name', 'email', 'phone', 'password', 'password_hash'], 'string', 'max' => 255],
            [['login'], 'unique'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'email' => 'Email',
            'phone' => 'Телефон',
            'status' => 'Статус',
            'role' => 'Роль',
            'created_at' => 'Создан',
            'updated_at' => 'Изменен',
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
            "login" => [
                "title" => $attributeLabels['login'],
                "type" => "string",
                "creatable" => true,
                "updatable" => true
            ],
            "first_name" => [
                "title" => $attributeLabels['first_name'],
                "type" => "string",
                "creatable" => true,
                "updatable" => true
            ],
            "last_name" => [
                "title" => $attributeLabels['last_name'],
                "type" => "string",
                "creatable" => true,
                "updatable" => true
            ],
            "email" => [
                "title" => $attributeLabels['email'],
                "type" => "string",
                "creatable" => true,
                "updatable" => true
            ],
            "phone" => [
                "title" => $attributeLabels['phone'],
                "type" => "string",
                "creatable" => true,
                "updatable" => true
            ],
            "status" => [
                "title" => $attributeLabels['status'],
                "type" => "string",
                "creatable" => true,
                "updatable" => true
            ],
            "role" => [
                "title" => $attributeLabels['role'],
                "type" => "string",
                "creatable" => true,
                "updatable" => true
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
            ],
        ];
    }

    /**
     * @param array $more_fields
     * @return array
     */
    public function getUserInfo($more_fields = [])
    {
        $fields = [
            'id' => $this->id,
            'login' => $this->login,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'role' => $this->role,
        ];
        foreach ($more_fields as $field){
            $fields[$field] = $this->$field;
        }
        return $fields;
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return User the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id): ?User
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface|null the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        try {
            if (!$token = Yii::$app->jwt->getParser()->parse($token)) {
                return null;
            }

            $user = self::find()
                ->where(['auth_key' => $token])
                ->andWhere(['id' => $token->getClaim('uid')])
                ->one();

            if ($user && $user instanceof User) {
                return $user;
            }

        } catch (\Exception $e) {
            return null;
        }

        return null;
    }


    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * The returned key is used to validate session and auto-login (if [[User::enableAutoLogin]] is enabled).
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string|null a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey(): string
    {
        return '';
    }

    /**
     * Validates the given auth key.
     *
     * @param string $authKey the given auth key
     * @return bool|null whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey): bool
    {
        return false;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param $insert
     * @return bool
     * @throws Exception
     */
    public function beforeSave($insert)
    {
        if($insert){
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        }
        return parent::beforeSave($insert);
    }

}
