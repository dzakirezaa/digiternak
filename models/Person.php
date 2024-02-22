<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Person extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%person}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    \yii\db\BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    \yii\db\BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['nik', 'first_name'], 'required'],
            [['nik'], 'string', 'max' => 16],
            [['nik'], 'match', 'pattern' => '/^\d{16}$/', 'message' => 'NIK must be a string of 16 digits.'],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Invalid birthdate format. Use YYYY-MM-DD format.'],
            [['birthdate'], 'validateBirthdate'],
            [['is_deleted'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 255],
            [['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => '/^[a-zA-Z\s]+$/', 'message' => 'Name must contain only letters and spaces.'],
            [['phone_number'], 'match', 'pattern' => '/^08\d{1,15}$/', 'message' => 'Invalid phone number format. Use 08xxxxxxxxxx format.'],
            [['address_id', 'gender_id', 'user_id'], 'integer'],
            [['gender_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gender::class, 'targetAttribute' => ['gender_id' => 'id']],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::class, 'targetAttribute' => ['address_id' => 'id']],
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
            'phone_number' => 'Phone Number',
            'address_id' => 'Address ID',
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
        $fields['user_id'] = 'user_id';

        // Menghapus fields yang tidak perlu disertakan dalam response JSON
        unset($fields['created_at'], $fields['updated_at'], $fields['is_deleted']);

        return $fields;
    }

    // Definisikan relasi dengan model Address
    public function getAddress()
    {
        return $this->hasOne(Address::class, ['id' => 'address_id']);
    }

    // Definisikan relasi dengan model Gender
    public function getGender()
    {
        return $this->hasOne(Gender::class, ['id' => 'gender_id']);
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

        // Check if the user is authenticated
        $userId = Yii::$app->user->identity->id;

        // Check if the user already has a person_id
        $user = User::findOne($userId);
        if ($user && $user->person_id === null) {
            // Update user's person_id with the newly created person's id
            $user->person_id = $this->id;
            $user->save(false); // Save without revalidating
        }

        // Update person's user_id if it's null
        if ($this->user_id === null) {
            $this->user_id = $userId;
            $this->save(false); // Save without revalidating
        }
    }
}