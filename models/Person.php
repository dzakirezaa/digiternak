<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Person extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%person}}';
    }

    public function rules()
    {
        return [
            [['nik', 'first_name'], 'required'],
            [['nik'], 'unique'],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d'],
            [['birthdate'], 'validateBirthdate'],
            [['is_deleted'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 255],
            [['gender_id', 'address_id', 'contact_id'], 'integer'],
            [['gender_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gender::class, 'targetAttribute' => ['gender_id' => 'id']],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::class, 'targetAttribute' => ['address_id' => 'id']],
            [['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nik' => 'NIK',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'birthdate' => 'Birthdate',
            'gender_id' => 'Gender ID',
            'address_id' => 'Address ID',
            'contact_id' => 'Contact ID',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // Menambahkan field terkait relasi untuk output JSON
        $fields['gender'] = 'gender';
        $fields['address'] = 'address';
        $fields['contact'] = 'contact';

        return $fields;
    }

    // Definisikan relasi dengan model Gender
    public function getGender()
    {
        return $this->hasOne(Gender::class, ['id' => 'gender_id']);
    }

    // Definisikan relasi dengan model Address
    public function getAddress()
    {
        return $this->hasOne(Address::class, ['id' => 'address_id']);
    }

    // Definisikan relasi dengan model Contact
    public function getContact()
    {
        return $this->hasOne(Contact::class, ['id' => 'contact_id']);
    }

    public function validateBirthdate($attribute, $params)
    {
        $today = new \DateTime();
        $birthdate = \DateTime::createFromFormat('Y-m-d', $this->$attribute);

        if ($birthdate >= $today) {
            $this->addError($attribute, 'Birthdate must be before today.');
        }
    }
}
