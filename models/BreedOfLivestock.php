<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class BreedOfLivestock extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%breed_of_livestock}}';
    }

    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['is_deleted'], 'boolean'],
            [['updated_at'], 'safe'],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['id', 'name'], 'unique', 'filter' => ['is_deleted' => 0]],
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
        return [
            'id',
            'name',
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