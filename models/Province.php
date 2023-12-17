<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Province extends ActiveRecord
{
    public static function tableName()
    {
        return 'province';
    }

    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['is_deleted'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'is_deleted' => 'Is Deleted',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // Tidak perlu menambahkan field terkait relasi untuk output JSON
        // Jika ingin menambahkan field relasi, dapat dilakukan di sini

        return $fields;
    }
}
