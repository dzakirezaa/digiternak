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
            [['eid', 'vid', 'name', 'birthdate', 'cage_id', 'type_of_livestock_id', 'breed_of_livestock_id', 'maintenance_id', 'source_id', 'ownership_status_id', 'reproduction_id', 'gender', 'age', 'chest_size', 'body_weight', 'health'], 'required', 'message' => '{attribute} cannot be blank'],
            [['cage_id'], 'required', 'message' => 'Please create a cage before adding livestock'],
            [['birthdate'], 'required', 'message' => 'Please enter the birthdate of the livestock'],
            [['created_at', 'updated_at'], 'safe'],
            [['eid', 'cage_id', 'type_of_livestock_id', 'breed_of_livestock_id', 'maintenance_id', 'source_id', 'ownership_status_id', 'reproduction_id'], 'integer'],
            [['chest_size', 'body_weight'], 'number'],
            [['name', 'gender', 'age', 'health', 'livestock_image'], 'string', 'max' => 255],
            [['vid'], 'string', 'max' => 10],
            [['eid', 'vid'], 'unique', 'message' => 'This {attribute} has already been taken'],
            [['vid'], 'match', 'pattern' => '/^[A-Z]{3}\d{4}$/', 'message' => '{attribute} must follow the pattern of three uppercase letters followed by four digits'], 
            [['is_deleted'], 'boolean'],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Invalid date format for {attribute}. Please use the YYYY-MM-DD format'],
            [['birthdate'], 'validateBirthdate'],
            [['livestock_image'], 'file', 'extensions' => ['png', 'jpg', 'jpeg'], 'maxSize' => 1024 * 1024 * 5, 'maxFiles' => 5, 'message' => 'Invalid file format or file size exceeded (maximum 5 MB)'],
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
            'cage_id' => 'Cage',
            'type_of_livestock_id' => 'Type of Livestock',
            'breed_of_livestock_id' => 'Breed of Livestock',
            'maintenance_id' => 'Maintenance',
            'source_id' => 'Source',
            'ownership_status_id' => 'Ownership Status',
            'reproduction_id' => 'Reproduction',
            'gender' => 'Gender',
            'age' => 'Age',
            'chest_size' => 'Chest Size',
            'body_weight' => 'Body Weight',
            'health' => 'Health',
            'livestock_image' => 'Livestock Image',
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'person_id',
            'eid',
            'vid',
            'name',
            'birthdate',
            'gender',
            'age',
            'chest_size',
            'body_weight',
            'health',
            'cage',
            'type_of_livestock',
            'breed_of_livestock',
            'maintenance',
            'source',
            'ownership_status',
            'reproduction',
        ];

        $fields['cage'] = function ($model) {
            return [
                'id' => $model->cage_id,
                'name' => $model->cage->name,
            ];
        };

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

        $fields['chest_size'] = function ($model) {
            return $model->chest_size . ' cm';
        };

        $fields['body_weight'] = function ($model) {
            return $model->body_weight . ' kg';
        };

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
            $this->addError($attribute, 'Birthdate must be today or before today');
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

    // Definisikan relasi dengan model Cage
    public function getCage()
    {
        return $this->hasOne(Cage::class, ['id' => 'cage_id']);
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $result = parent::toArray($fields, $expand, $recursive);
        
        if ($this === null) {
            return [];
        }

        return $result;
    }
}
