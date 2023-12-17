<?php

namespace app\models;

use yii\db\ActiveRecord;

class UserRole extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_role';
    }

    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['name'], 'string'],
            [['name'], 'in', 'range' => ['Peternak', 'Admin', 'Customer'], 'message' => 'Invalid user role. Accepted values are \'Peternak\', \'Admin\', or \'Customer\'.'],
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
