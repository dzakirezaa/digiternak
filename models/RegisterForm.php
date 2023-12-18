<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class RegisterForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_repeat;
    public $role_id; 

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password', 'password_repeat', 'role_id'], 'required'],
            [['username'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address has already been taken.'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'This username has already been taken.'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => "Passwords don't match."],
            ['password', 'validatePasswordComplexity'],
            ['role_id', 'in', 'range' => [1, 2, 3], 'message' => 'Invalid user role.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'password_repeat' => 'Repeat Password',
            'role_id' => 'User Role',
        ];
    }

    /**
     * Validate password complexity.
     * Ensure the password contains at least one uppercase letter, one lowercase letter, and one digit.
     *
     * @param string $attribute
     * @param array $params
     */
    public function validatePasswordComplexity($attribute, $params)
    {
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $this->$attribute)) {
            $this->addError($attribute, 'Password must contain at least one uppercase letter, one lowercase letter, and one digit.');
        }
    }

    /**
     * Register a new user
     *
     * 
     */
    public function register()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->role_id = $this->role_id;
            $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
            $user->access_token = Yii::$app->security->generateRandomString();
            $user->status = User::STATUS_ACTIVE;

            // Tambahkan kode untuk generate auth_key
            $user->auth_key = Yii::$app->security->generateRandomString();

            if ($user->save(false)) {
                Yii::debug('User saved successfully', __METHOD__);
                return [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'role_id' => $user->role_id,
                        'auth_key' => $user->auth_key, // Tambahkan auth_key ke respons
                    ],
                    'token' => $user->access_token,
                ];
            } else {
                Yii::debug('Failed to save user: ' . print_r($user->errors, true), __METHOD__);
                $this->addErrors($user->errors);
            }
        } else {
            Yii::debug('Validation failed: ' . print_r($this->errors, true), __METHOD__);
        }

        return null;
    }
}
