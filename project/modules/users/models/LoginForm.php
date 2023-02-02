<?php

namespace app\modules\users\models;

use sizeg\jwt\Jwt;
use Yii;
use yii\base\Model;

/**
 * Class LoginForm
 *
 * @property-read null|User $user
 */
class LoginForm extends Model
{
    //Время жизни токена 7 дней,
    public const AUTH_TOKEN_EXPIRE_TIME = 60 * 60 * 24 * 7;

    /** @var string */
    public $login;

    /** @var string */
    public $password;

    /** @var User $_user */
    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            // username and password are both required
            [['login', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный логин или или пароль');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            if ($this->getUser()) {
                /** @var $jwt Jwt */
                $jwt = Yii::$app->jwt;
                $expire_at = time() + static::AUTH_TOKEN_EXPIRE_TIME;
                $signer = $jwt->getSigner('HS256');
                $key = $jwt->getKey();
                $access_token = $jwt->getBuilder()
                    ->issuedAt(time())
                    ->expiresAt($expire_at)
                    ->withClaim('uid', $this->_user->id)
                    ->getToken($signer, $key);
                $this->_user->auth_key = (string)$access_token;
                $this->_user->save();
                Yii::$app->user->login($this->_user, static::AUTH_TOKEN_EXPIRE_TIME);
                return $access_token;
            }
        }
        return false;
    }

    /**
     * @return User|null
     */
    protected function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['login' => $this->login]);
        }
        return $this->_user;
    }
}