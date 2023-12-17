<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Reproduction extends ActiveRecord
{
    public static function tableName()
    {
        return 'reproduction';
    }

    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['is_deleted'], 'boolean'],
            [['updated_at'], 'safe'],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 255],
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