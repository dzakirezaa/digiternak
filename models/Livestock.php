<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Livestock extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%livestock}}';
    }

    public function rules()
    {
        return [
            [['eid', 'vid', 'name', 'type_of_livestock_id', 'breed_of_livestock_id', 'maintenance_id', 'source_id', 'ownership_status_id', 'reproduction_id', 'gender', 'age', 'chest_size', 'body_weight', 'health', 'bcs'], 'required'],
            [['birthdate', 'created_at', 'updated_at'], 'safe'],
            [['type_of_livestock_id', 'breed_of_livestock_id', 'maintenance_id', 'source_id', 'ownership_status_id', 'reproduction_id', 'bcs'], 'integer'],
            [['chest_size', 'body_weight'], 'number'],
            [['eid', 'vid', 'name', 'gender', 'age', 'health'], 'string', 'max' => 255],
            [['eid'], 'unique'],
            [['vid'], 'unique'],
            [['is_deleted'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eid' => 'EID',
            'vid' => 'VID',
            'name' => 'Name',
            'birthdate' => 'Birthdate',
            'type_of_livestock_id' => 'Type of Livestock ID',
            'breed_of_livestock_id' => 'Breed of Livestock ID',
            'maintenance_id' => 'Maintenance ID',
            'source_id' => 'Source ID',
            'ownership_status_id' => 'Ownership Status ID',
            'reproduction_id' => 'Reproduction ID',
            'gender' => 'Gender',
            'age' => 'Age',
            'chest_size' => 'Chest Size',
            'body_weight' => 'Body Weight',
            'health' => 'Health',
            'bcs' => 'BCS',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'eid',
            'vid',
            'name',
            'birthdate',
            'type_of_livestock_id',
            'breed_of_livestock_id',
            'maintenance_id',
            'source_id',
            'ownership_status_id',
            'reproduction_id',
            'gender',
            'age',
            'chest_size',
            'body_weight',
            'health',
            'bcs',
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

    // Definisikan relasi dengan model TypeOfLivestock
    public function getTypeOfLivestock()
    {
        return $this->hasOne(TypeOfLivestock::class, ['id' => 'type_of_livestock_id']);
    }

    // Definisikan relasi dengan model BreedOfLivestock
    public function getBreedOfLivestock()
    {
        return $this->hasOne(BreedOfLivestock::class, ['id' => 'breed_of_livestock_id']);
    }

    // Definisikan relasi dengan model Maintenance
    public function getMaintenance()
    {
        return $this->hasOne(Maintenance::class, ['id' => 'maintenance_id']);
    }

    // Definisikan relasi dengan model Source
    public function getSource()
    {
        return $this->hasOne(Source::class, ['id' => 'source_id']);
    }

    // Definisikan relasi dengan model OwnershipStatus
    public function getOwnershipStatus()
    {
        return $this->hasOne(OwnershipStatus::class, ['id' => 'ownership_status_id']);
    }

    // Definisikan relasi dengan model Reproduction
    public function getReproduction()
    {
        return $this->hasOne(Reproduction::class, ['id' => 'reproduction_id']);
    }
}
