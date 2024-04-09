<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Cage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%cage}}';
    }

    public function rules()
    {
        return [
            [['name', 'location'], 'required', 'message' => '{attribute} cannot be blank'],
            [['location'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 10],
            ['person_id', 'integer'],
            [['name'], 'match', 'pattern' => '/^[A-Za-z0-9\s]{3,10}$/', 'message' => '{attribute} must be between 3 and 10 characters long and may contain letters, numbers, and spaces only'],
            [['location'], 'match', 'pattern' => '/^[A-Za-z0-9\s]{3,255}$/', 'message' => '{attribute} must be between 3 and 255 characters long and may contain letters, numbers, and spaces only'],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'location',
            // 'livestocks' => function ($model) {
            //     return $model->livestocks;
            // }
        ];
    }

    public function getLivestocks()
    {
        return $this->hasMany(Livestock::class, ['cage_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Ambil person_id dari user yang sedang login
        $personId = Yii::$app->user->identity->person_id;

        // Simpan person_id
        $this->updateAttributes(['person_id' => $personId]);
    }
}
