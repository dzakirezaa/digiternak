<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Cage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%cage}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ]
        ];
    }

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public function rules()
    {
        return [
            [['name', 'location', 'description'], 'required', 'on' => self::SCENARIO_CREATE, 'message' => '{attribute} tidak boleh kosong.'],
            [['name', 'location', 'description'], 'safe', 'on' => self::SCENARIO_UPDATE],
            [['location', 'description'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 50],
            ['name', 'validateCageName'],
            ['user_id', 'integer'],
            [['name'], 'match', 'pattern' => '/^[A-Za-z0-9\s]{3,30}$/', 'message' => '{attribute} terdiri dari 3 sampai 30 karakter dan hanya boleh berisi huruf, angka, dan spasi.'],
            [['location', 'description'], 'match', 'pattern' => '/^[A-Za-z0-9\s]{3,255}$/', 'message' => '{attribute} terdiri dari 3 sampai 255 karakter dan hanya boleh berisi huruf, angka, dan spasi.'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name', 'location', 'description', 'user_id'];
        $scenarios[self::SCENARIO_UPDATE] = ['name', 'location', 'description'];
        return $scenarios;
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'location',
            'description',
            'livestocks' => function ($model) {
                return array_map(function ($livestock) {
                    return $livestock->id;
                }, $model->livestocks);
            },
            'created_at',
            'updated_at',
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
            'name' => 'Nama Kandang',
            'location' => 'Lokasi Kandang',
            'description' => 'Deskripsi Kandang',
            'user_id' => 'User ID',
        ];
    }

    public function validateCageName($attribute, $params)
    {
        if (!$this->isNewRecord && !$this->isAttributeChanged($attribute)) {
            return;
        }

        $userId = Yii::$app->user->identity->id;
        $existingCage = Cage::find()
            ->where(['name' => $this->$attribute, 'user_id' => $userId])
            ->one();

        if ($existingCage) {
            $this->addError($attribute, 'Anda sudah memiliki kandang dengan nama yang sama. Silakan gunakan nama yang berbeda.');
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            // Get user_id from the currently logged in user
            $userId = Yii::$app->user->identity->id;

            // Save user_id
            $this->updateAttributes(['user_id' => $userId]);
        }
    }
}