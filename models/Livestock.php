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
            [['eid', 'vid', 'name', 'birthdate', 'type_of_livestock_id', 'breed_of_livestock_id', 'maintenance_id', 'source_id', 'ownership_status_id', 'reproduction_id', 'gender', 'age', 'chest_size', 'body_weight', 'health'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['eid', 'type_of_livestock_id', 'breed_of_livestock_id', 'maintenance_id', 'source_id', 'ownership_status_id', 'reproduction_id'], 'integer'],
            [['chest_size', 'body_weight'], 'number'],
            [['name', 'gender', 'age', 'health', 'livestock_image'], 'string', 'max' => 255],
            [['vid', 'cage'], 'string', 'max' => 10],
            [['eid', 'vid'], 'unique'],
            [['vid'], 'match', 'pattern' => '/^[A-Z]{3}\d{4}$/'],
            [['cage'], 'match', 'pattern' => '/^[A-Z]{3}\d{3}$/'], 
            [['is_deleted'], 'boolean'],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d'],
            [['birthdate'], 'validateBirthdate'],
            [['livestock_image'], 'file', 'extensions' => ['png', 'jpg', 'jpeg'], 'maxSize' => 1024 * 1024 * 5, 'maxFiles' => 1], // Maks 5 MB, hanya satu file
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eid' => 'EID',
            'vid' => 'Visual ID',
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
            // 'bcs_id' => 'BCS',
            'cage' => 'Cage',
            'livestock_image' => 'Livestock Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['type_of_livestock'] = function ($model) {
            return [
                'id' => $model->type_of_livestock_id,
                'name' => $model->typeOfLivestock->name,
            ];
        };
    
        $fields['breed_of_livestock'] = function ($model) {
            return [
                'id' => $model->breed_of_livestock_id,
                'name' => $model->breedOfLivestock->name,
            ];
        };
    
        $fields['maintenance'] = function ($model) {
            return [
                'id' => $model->maintenance_id,
                'name' => $model->maintenance->name,
            ];
        };
    
        $fields['source'] = function ($model) {
            return [
                'id' => $model->source_id,
                'name' => $model->source->name,
            ];
        };
    
        $fields['ownership_status'] = function ($model) {
            return [
                'id' => $model->ownership_status_id,
                'name' => $model->ownershipStatus->name,
            ];
        };
    
        $fields['reproduction'] = function ($model) {
            return [
                'id' => $model->reproduction_id,
                'name' => $model->reproduction->name,
            ];
        };

        // $fields['bcs'] = function ($model) {
        //     return $model->bodyCountScore->name;
        // };

        $fields['chest_size'] = function ($model) {
            return $model->chest_size . ' cm';
        };

        $fields['body_weight'] = function ($model) {
            return $model->body_weight . ' kg';
        };

        $fields['created_at'] = function ($model) {
            return Yii::$app->formatter->asDatetime($model->created_at, 'php:Y-m-d H:i:s');
        };
    
        $fields['updated_at'] = function ($model) {
            return Yii::$app->formatter->asDatetime(time(), 'php:Y-m-d H:i:s');
        };

        unset($fields['type_of_livestock_id'], $fields['breed_of_livestock_id'], $fields['maintenance_id'], $fields['source_id'], $fields['ownership_status_id'], $fields['reproduction_id'], $fields['is_deleted']);

        return $fields;
    }

    public function extraFields()
    {
        return [
            'is_deleted',
        ];
    }

    public function validateBirthdate($attribute, $params)
    {
        $today = new \DateTime();
        $birthdate = \DateTime::createFromFormat('Y-m-d', $this->$attribute);

        if ($birthdate >= $today) {
            $this->addError($attribute, 'Birthdate must be before today.');
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Ambil person_id dari user yang sedang login
        $personId = Yii::$app->user->identity->person_id;

        // Simpan person_id
        $this->updateAttributes(['person_id' => $personId]);
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

    public function getPerson()
    {
        return $this->hasOne(Person::class, ['id' => 'person_id']);
    }

    // // Definisikan relasi dengan model BodyCountScore
    // public function getBodyCountScore()
    // {
    //     return $this->hasOne(BodyCountScore::class, ['id' => 'bcs_id']);
    // }
}
