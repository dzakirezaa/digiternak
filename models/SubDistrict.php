<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class SubDistrict extends ActiveRecord
{
    public static function tableName()
    {
        return 'sub_district';
    }

    public function rules()
    {
        return [
            [['id', 'district_id', 'name'], 'required'],
            [['id', 'district_id'], 'integer'],
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
            'district_id' => 'District ID',
            'name' => 'Name',
            'is_deleted' => 'Is Deleted',
            'updated_at' => 'Updated At',
        ];
    }

    // Definisikan relasi dengan model District
    public function getDistrict()
    {
        return $this->hasOne(District::class, ['id' => 'district_id']);
    }

    public function fields()
    {
        $fields = parent::fields();

        // Menambahkan field terkait relasi untuk output JSON
        $fields['district'] = 'district';

        return $fields;
    }
}
