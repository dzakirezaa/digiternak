<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class PostCode extends ActiveRecord
{
    public static function tableName()
    {
        return 'post_code';
    }

    public function rules()
    {
        return [
            [['province', 'city', 'district', 'sub_district', 'postal_code'], 'required'],
            [['is_deleted'], 'boolean'],
            [['updated_at'], 'safe'],
            [['province', 'city', 'district', 'sub_district', 'postal_code'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'province' => 'Province',
            'city' => 'City',
            'district' => 'District',
            'sub_district' => 'Sub District',
            'postal_code' => 'Postal Code',
            'is_deleted' => 'Is Deleted',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'province',
            'city',
            'district',
            'sub_district',
            'postal_code',
            'updated_at' => function ($model) {
                return Yii::$app->formatter->asDatetime($model->updated_at);
            },
        ];
    }

    public function extraFields()
    {
        return [
            'is_deleted',
        ];
    }
}