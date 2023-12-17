<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class City extends ActiveRecord
{
    public static function tableName()
    {
        return 'city';
    }

    public function rules()
    {
        return [
            [['id', 'province_id', 'name'], 'required'],
            [['id', 'province_id'], 'integer'],
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
            'province_id' => 'Province ID',
            'name' => 'Name',
            'is_deleted' => 'Is Deleted',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // Menambahkan field terkait relasi untuk output JSON
        $fields['province'] = 'province';

        return $fields;
    }


    /**
     * Gets query for [[Province]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvince()
    {
        return $this->hasOne(Province::class, ['id' => 'province_id']);
    }
}
