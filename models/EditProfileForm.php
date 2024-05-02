<?php

namespace app\models;

use yii\base\Model;

class EditProfileForm extends Model
{
    public $username;
    public $nik;
    public $full_name;
    public $birthdate;
    public $phone_number;
    public $gender_id;
    public $address;
    public $is_deleted;
    public $user_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['username', 'string', 'max' => 50],
            [['nik', 'phone_number'], 'string', 'max' => 16],
            ['nik', 'unique', 'targetClass' => User::class, 'message' => 'NIK has already been taken'],
            [['nik'], 'match', 'pattern' => '/^\d{16}$/', 'message' => 'NIK must be a string of 16 digits.'],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Invalid birthdate format. Use YYYY-MM-DD format.'],
            [['birthdate'], 'validateBirthdate'],
            [['address'], 'string', 'max' => 255],
            [['full_name', 'address'], 'string', 'max' => 255],
            [['full_name'], 'match', 'pattern' => '/^[a-zA-Z\s]+$/', 'message' => 'Name must contain only letters and spaces.'],
            [['phone_number'], 'match', 'pattern' => '/^08\d{1,15}$/', 'message' => 'Invalid phone number format. Use 08xxxxxxxxxx format.'],
            [['gender_id'], 'integer'],
            [['gender_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gender::class, 'targetAttribute' => ['gender_id' => 'id']],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'nik' => 'NIK',
            'full_name' => 'Full Name',
            'birthdate' => 'Birthdate',
            'phone_number' => 'Phone Number',
            'gender_id' => 'Gender',
            'address' => 'Address',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        // Keep all attributes before validation
        $this->setAttributes([
            'username' => $this->username,
            'nik' => $this->nik,
            'full_name' => $this->full_name,
            'birthdate' => $this->birthdate,
            'phone_number' => $this->phone_number,
            'gender_id' => $this->gender_id,
            'address' => $this->address,
        ]);

        return parent::beforeValidate();
    }

    /**
     * Validates the birthdate.
     * This method serves as the inline validation for birthdate.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateBirthdate($attribute, $params)
    {
        $today = new \DateTime();
        $birthdate = \DateTime::createFromFormat('Y-m-d', $this->$attribute);

        if ($birthdate >= $today) {
            $this->addError($attribute, 'Birthdate must be before today.');
        }
    }
}
