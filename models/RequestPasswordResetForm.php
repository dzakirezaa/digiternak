<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RequestPasswordResetForm extends Model
{
    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was sent
     */
    public function sendEmail()
    {
        if ($this->validate()) {
            $user = User::findOne(['email' => $this->email]);
            if ($user) {
                return $user->sendPasswordResetEmail();
            }
        }
        return false;
    }
}
