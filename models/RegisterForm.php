<?php

namespace app\models;

use Yii;
use yii\base\Model;

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
            ['role_id', 'in', 'range' => ['Peternak', 'Admin', 'Customer'], 'message' => 'Invalid user role.'],
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
     * Register a new user and generate JWT token.
     *
     * @return array|null the saved user data with JWT token or null if saving fails
     */
    public function register()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->status = User::STATUS_ACTIVE; // Sesuaikan dengan definisi status Anda
            $user->role_id = $this->role_id; // Set role_id

            if ($user->save()) {
                // Generate JWT token
                $jwt = Yii::$app->jwt;
                $token = $jwt->getBuilder()
                    ->setIssuer(Yii::$app->request->absoluteUrl)
                    ->setSubject((string)$user->id)
                    ->setAudience(Yii::$app->request->absoluteUrl)
                    ->setIssuedAt(time())
                    ->setExpiration(time() + 3600) // Token expiration (1 hour)
                    ->setId(Yii::$app->security->generateRandomString(16), true)
                    ->set('uid', $user->id)
                    ->sign($jwt->getSigner(), $jwt->key)
                    ->getToken();

                return [
                    'user' => $user,
                    'token' => (string)$token,
                ];
            }
        }

        return null;
    }
}
