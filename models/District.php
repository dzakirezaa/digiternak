<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class District extends ActiveRecord
{
    public static function tableName()
    {
        return 'district';
    }

    public function rules()
    {
        return [
            [['id', 'city_id', 'name'], 'required'],
            [['id', 'city_id'], 'integer'],
            [['id'], 'unique'],
            [['name'], 'string', 'max' => 255],
            [['updated_at'], 'safe'],
            [['is_deleted'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => 'City ID',
            'name' => 'Name',
            'is_deleted' => 'Is Deleted',
            'updated_at' => 'Updated At',
        ];
    }

    // Definisikan relasi dengan model City
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    public function fields()
    {
        $fields = parent::fields();

        // Menambahkan field terkait relasi untuk output JSON
        $fields['city'] = 'city';

        return $fields;
    }
}
