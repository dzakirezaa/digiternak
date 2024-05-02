<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Livestock extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%livestock}}';
    }

    public function rules()
    {
        return [
            [['name', 'birthdate', 'gender', 'age', 'chest_size', 'body_weight', 'health'], 'required', 'message' => '{attribute} cannot be blank'],
            [['type_of_livestock_id'], 'required', 'message' => 'Please select the type of livestock'],
            [['breed_of_livestock_id'], 'required', 'message' => 'Please select the breed of livestock'],
            [['maintenance_id'], 'required', 'message' => 'Please select the maintenance of the livestock'],
            [['source_id'], 'required', 'message' => 'Please select the source of the livestock'],
            [['ownership_status_id'], 'required', 'message' => 'Please select the ownership status of the livestock'],
            [['reproduction_id'], 'required', 'message' => 'Please select the reproduction of the livestock'],
            [['birthdate'], 'required', 'message' => 'Please enter the birthdate of the livestock'],
            ['name', 'validateLivestockName'],
            [['eid', 'cage_id', 'type_of_livestock_id', 'breed_of_livestock_id', 'maintenance_id', 'source_id', 'ownership_status_id', 'reproduction_id'], 'integer'],
            [['chest_size', 'body_weight'], 'number'],
            [['name', 'gender', 'age', 'health', 'livestock_image'], 'string', 'max' => 255],
            [['vid'], 'string', 'max' => 10],
            [['eid', 'vid'], 'unique', 'message' => 'This {attribute} has already been taken'],
            ['eid', 'string', 'length' => 32, 'message' => 'EID must be a 32-digit number'],
            ['eid', 'match', 'pattern' => '/^\d{32}$/', 'message' => 'EID must be a 32-digit number'],
            [['vid'], 'match', 'pattern' => '/^[A-Z]{3}[0-9]{4}$/', 'message' => 'Visual ID must follow the pattern of three uppercase letters followed by four digits', 'on' => 'create'],
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
            'user_id',
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
            if ($model->cage !== null) {
                return [
                    'id' => $model->cage_id,
                    'name' => $model->cage->name,
                ];
            } else {
                return null;
            }
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

        $fields['livestock_images'] = function ($model) {
            return array_map(function ($livestockImage) {
                return $livestockImage->image_path;
            }, $model->livestockImages);
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

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                // $this->eid = $this->generateEid();
                $this->vid = $this->generateVid();
            }
            return true;
        }
        return false;
    }

    // private function generateEid()
    // {
    //     // Generate a random eid of 32 digits
    //     $eid = sprintf('%032d', mt_rand(0, 99999999999999999999999999999999));
    //     return $eid;
    // }

    private function generateVid()
    {
        // Generate 3 random uppercase letters
        $letters = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);

        // Generate a 4 digit random number
        $numbers = sprintf('%04d', mt_rand(0, 9999));

        // Combine the letters and numbers to form the VID
        $vid = $letters . $numbers;

        return $vid;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Get user_id from the currently logged in user
        $userId = Yii::$app->user->identity->id;

        // Save user_id
        $this->updateAttributes(['user_id' => $userId]);
    }

    public function validateLivestockName($attribute, $params)
    {
        $userId = Yii::$app->user->identity->id;
        $existingLivestock = Livestock::find()
            ->where(['name' => $this->$attribute, 'user_id' => $userId])
            ->one();

        if ($existingLivestock) {
            $this->addError($attribute, 'You have already created a livestock with this name.');
        }
    }

    // Definisikan relasi dengan model LivestockImage
    public function getLivestockImages()
    {
        return $this->hasMany(LivestockImage::class, ['livestock_id' => 'id']);
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

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
