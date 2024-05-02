<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * RegisterForm is the model behind the user registration form.
 */
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
            ['role_id', 'integer', 'message' => 'Role Id must be an integer.'],
            ['role_id', 'exist', 'skipOnError' => true, 'targetClass' => UserRole::class, 'targetAttribute' => ['role_id' => 'id']],
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
     * @return array|null Return user data if registration is successful, otherwise return null.
     */
    public function register()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->role_id = $this->role_id;
            $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
            $user->status = User::STATUS_ACTIVE;
            $user->verification_token = Yii::$app->security->generateRandomString();

            if ($user->save(false)) {
                return $user;
            } else {
                $this->addErrors($user->errors);
            }
        }

        return null;
    }
}
