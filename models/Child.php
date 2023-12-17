<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Child extends ActiveRecord
{
    public static function tableName()
    {
        return 'child';
    }

    public function rules()
    {
        return [
            [['livestock_id', 'name'], 'required'],
            [['is_deleted'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['livestock_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'livestock_id' => 'Livestock ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'is_deleted' => 'Is Deleted',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'livestock_id',
            'name',
            'created_at' => function ($model) {
                return Yii::$app->formatter->asDatetime($model->created_at);
            },
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