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
    public $verification_token;

    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'required', 'message' => 'Atribut tidak boleh kosong.'],
            [['username', 'email'], 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Email sudah digunakan oleh pengguna lain.'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'Username sudah digunakan oleh pengguna lain.'],
            ['password', 'string', 'min' => 8, 'message' => 'Password minimal terdiri dari 8 karakter.'],
            ['password', 'validatePasswordComplexity'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
        ];
    }

    public function validatePasswordComplexity($attribute, $params)
    {
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $this->$attribute)) {
            $this->addError($attribute, 'Password harus mengandung setidaknya satu huruf besar, satu huruf kecil, dan satu angka.');
        }
    }

    public function register()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->status = User::STATUS_ACTIVE;
        $user->verification_token = $this->verification_token;

        return $user->save() ? $user : null;
    }
}