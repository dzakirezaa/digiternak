<?php

namespace app\models;

use yii\base\Model;

class EditProfileForm extends Model
{
    public $username;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'string', 'max' => 50],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Username',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        // Unset all attributes except username before validation
        $this->setAttributes(['username' => $this->username]);

        return parent::beforeValidate();
    }
}
