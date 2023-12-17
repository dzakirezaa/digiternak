<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Contact extends ActiveRecord
{
    public static function tableName()
    {
        return 'contact';
    }

    public function rules()
    {
        return [
            [['person_id', 'phone_number'], 'required'],
            [['person_id'], 'integer'],
            [['phone_number'], 'string', 'max' => 255],
            [['is_deleted'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'person_id' => 'Person ID',
            'phone_number' => 'Phone Number',
            'created_at' => 'Created At',
            'is_deleted' => 'Is Deleted',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'person_id',
            'phone_number',
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

    // Definisikan relasi dengan model Person
    public function getPerson()
    {
        return $this->hasOne(Person::class, ['id' => 'person_id']);
    }
}