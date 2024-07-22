<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
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
                $this->addError($attribute, 'Username atau password salah. Silakan coba lagi.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return string|null the JWT token if the user is logged in successfully, otherwise null
     */
    public function login()
    {
        if ($this->validate() && $this->getUser()) {
            // Perbarui auth_key jika login berhasil
            $this->_user->generateJwt();
            $this->_user->save(false);

            // Mengembalikan auth_key sebagai token otentikasi
            return $this->_user->auth_key;
        }
        return null;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}

// public function login()
//     {
//         if ($this->validate()) {
//             $user = $this->getUser();
//             if ($user) {
//                 // Generate JWT token
//                 $jwtToken = $user->generateJwt();

//                 // Use Yii2's cache to store the JWT token
//                 // The key is prefixed with 'jwt_' for clarity, adjust as needed
//                 $cacheKey = 'jwt_' . $user->id;
//                 $expiration = 3600; // Token expiration time in seconds, adjust as needed

//                 // Save JWT token to cache
//                 Yii::$app->cache->set($cacheKey, $jwtToken, $expiration);

//                 // Return the JWT token
//                 return $jwtToken;
//             }
//         }
//         return null;
//     }