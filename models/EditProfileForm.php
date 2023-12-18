<?php

namespace app\models;

use yii\base\Model;

class EditProfileForm extends Model
{
    public $username;
    public $email;

    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            ['email', 'email'],
        ];
    }

    public function editProfile(User $user)
    {
        if ($this->validate()) {
            $user->username = $this->username;
            $user->email = $this->email;

            return $user->save();
        }

        return false;
    }
}
