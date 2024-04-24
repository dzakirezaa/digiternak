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
            ['name', 'validateCageName'],
            ['user_id', 'integer'],
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
            'livestocks' => function ($model) {
                return array_map(function ($livestock) {
                    return $livestock->id;
                }, $model->livestocks);
            }
        ];
    }

    public function getLivestocks()
    {
        return $this->hasMany(Livestock::class, ['cage_id' => 'id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'location' => 'Location',
            'user_id' => 'User ID',
        ];
    }

    public function validateCageName($attribute, $params)
    {
        $userId = Yii::$app->user->identity->id;
        $existingCage = Cage::find()
            ->where(['name' => $this->$attribute, 'user_id' => $userId])
            ->one();

        if ($existingCage) {
            $this->addError($attribute, 'You have already created a cage with this name.');
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Get user_id from the currently logged in user
        $userId = Yii::$app->user->identity->id;

        // Save user_id
        $this->updateAttributes(['user_id' => $userId]);
    }
}
