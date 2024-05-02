<?php

namespace app\models;

use yii\db\ActiveRecord;

class UserRole extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_role}}';
    }

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function fields()
    {
        return [
            'name',
        ];
    }
}