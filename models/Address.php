<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Address extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%address}}';
    }

    public function rules()
    {
        return [
            [['person_id', 'province_id', 'city_id', 'district_id', 'sub_district_id'], 'required'],
            [['person_id', 'province_id', 'city_id', 'district_id', 'sub_district_id', 'post_code', 'type_of_home_id'], 'integer'],
            [['detail_address', 'landmark_info'], 'string', 'max' => 255],
            [['is_deleted'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['person_id' => 'id']],
            [['province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Province::class, 'targetAttribute' => ['province_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['district_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::class, 'targetAttribute' => ['district_id' => 'id']],
            [['sub_district_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubDistrict::class, 'targetAttribute' => ['sub_district_id' => 'id']],
            [['post_code'], 'exist', 'skipOnError' => true, 'targetClass' => PostCode::class, 'targetAttribute' => ['post_code' => 'id']],
            [['type_of_home_id'], 'exist', 'skipOnError' => true, 'targetClass' => TypeOfHome::class, 'targetAttribute' => ['type_of_home_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'person_id' => 'Person ID',
            'province_id' => 'Province ID',
            'city_id' => 'City ID',
            'district_id' => 'District ID',
            'sub_district_id' => 'Sub District ID',
            'detail_address' => 'Detail Address',
            'landmark_info' => 'Landmark Info',
            'post_code' => 'Post Code',
            'type_of_home_id' => 'Type of Home ID',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // Menambahkan field terkait relasi untuk output JSON
        $fields['person'] = 'person';
        $fields['province'] = 'province';
        $fields['city'] = 'city';
        $fields['district'] = 'district';
        $fields['sub_district'] = 'subDistrict';
        $fields['post_code'] = 'postCode';
        $fields['type_of_home'] = 'typeOfHome';

        return $fields;
    }

    // Definisikan relasi dengan model Person
    public function getPerson()
    {
        return $this->hasOne(Person::class, ['id' => 'person_id']);
    }

    // Definisikan relasi dengan model Province
    public function getProvince()
    {
        return $this->hasOne(Province::class, ['id' => 'province_id']);
    }

    // Definisikan relasi dengan model City
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    // Definisikan relasi dengan model District
    public function getDistrict()
    {
        return $this->hasOne(District::class, ['id' => 'district_id']);
    }

    // Definisikan relasi dengan model SubDistrict
    public function getSubDistrict()
    {
        return $this->hasOne(SubDistrict::class, ['id' => 'sub_district_id']);
    }

    // Definisikan relasi dengan model PostCode
    public function getPostCode()
    {
        return $this->hasOne(PostCode::class, ['id' => 'post_code']);
    }

    // Definisikan relasi dengan model TypeOfHome
    public function getTypeOfHome()
    {
        return $this->hasOne(TypeOfHome::class, ['id' => 'type_of_home_id']);
    }
}
