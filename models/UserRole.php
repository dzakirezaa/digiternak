<?php

namespace app\models;

use yii\db\ActiveRecord;

class UserRole extends ActiveRecord
{
    // Konstanta untuk setiap role
    const ROLE_PETERNAK = 1;
    const ROLE_ADMIN = 2;
    const ROLE_CUSTOMER = 3;

    // Tambahkan konstanta ini agar bisa digunakan dalam aturan validasi
    public static function roles()
    {
        return [
            self::ROLE_PETERNAK => 'Peternak',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_CUSTOMER => 'Customer',
        ];
    }

    public static function tableName()
    {
        return '{{%user_role}}';
    }

    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['name'], 'string'],
            // Modifikasi validasi agar sesuai dengan konstanta
            [['name'], 'in', 'range' => array_values(self::roles())],
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

