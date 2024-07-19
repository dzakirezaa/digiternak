<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;

class Livestock extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%livestock}}';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'birthdate', 'type_of_livestock', 'breed_of_livestock', 'purpose', 'maintenance', 'source', 'ownership_status', 'reproduction', 'gender', 'age', 'chest_size', 'body_weight', 'health'], 'required', 'message' => '{attribute} tidak boleh kosong.'],
            [['birthdate'], 'required', 'message' => 'Masukkan tanggal lahir ternak.'],
            [['user_id', 'cage_id', 'age'], 'integer'],
            ['name', 'validateLivestockName'],
            [['chest_size', 'body_weight'], 'number'],
            ['name', 'string', 'max' => 255],
            [['livestock_image'], 'string'],
            [['eid', 'vid'], 'unique', 'message' => '{attribute} sudah digunakan oleh ternak lain.'],
            [['name'], 'match', 'pattern' => '/^[A-Za-z0-9\s]{3,255}$/', 'message' => 'Nama harus terdiri dari 3 sampai 255 karakter dan hanya boleh berisi huruf, angka, dan spasi.'],
            ['eid', 'string', 'length' => 32],
            ['eid', 'match', 'pattern' => '/^\d{32}$/', 'message' => 'EID harus terdiri dari 32 digit.'],
            [['vid'], 'string', 'max' => 10],
            [['vid'], 'match', 'pattern' => '/^[A-Z]{3}[0-9]{4}$/', 'message' => 'Visual ID harus mengikuti pola tiga huruf besar diikuti empat digit.', 'on' => 'create'],
            [['created_at', 'updated_at', 'birthdate'], 'safe'],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Format tanggal tidak valid. Tolong gunakan format YYYY-MM-DD.'],
            [['birthdate'], 'validateBirthdate'],
            [['livestock_image'], 'file', 'extensions' => ['png', 'jpg', 'jpeg'], 'maxSize' => 1024 * 1024 * 5, 'maxFiles' => 5, 'message' => 'Format file tidak valid atau ukuran file terlalu besar (maksimal 5 MB).'],

            // Enum validation rules
            ['gender', 'in', 'range' => ['Jantan', 'Betina']],
            ['type_of_livestock', 'in', 'range' => ['Kambing', 'Sapi']],
            ['breed_of_livestock', 'in', 'range' => ['Madura', 'Bali', 'Limousin', 'Brahman']],
            ['purpose', 'in', 'range' => ['Indukan', 'Penggemukan', 'Tabungan', 'Belum Tahu']],
            ['maintenance', 'in', 'range' => ['Kandang', 'Gembala', 'Campuran']],
            ['source', 'in', 'range' => ['Sejak Lahir', 'Bantuan Pemerintah', 'Beli', 'Beli dari Luar Kelompok', 'Beli dari Dalam Kelompok', 'Inseminasi Buatan', 'Kawin Alam', 'Tidak Tahu']],
            ['ownership_status', 'in', 'range' => ['Sendiri', 'Kelompok', 'Titipan']],
            ['reproduction', 'in', 'range' => ['Tidak Bunting', 'Bunting < 1 bulan', 'Bunting 1 bulan', 'Bunting 2 bulan', 'Bunting 3 bulan', 'Bunting 4 bulan', 'Bunting 5 bulan', 'Bunting 6 bulan', 'Bunting 7 bulan', 'Bunting 8 bulan', 'Bunting 9 bulan', 'Bunting 10 bulan', 'Bunting 11 bulan', 'Bunting > 11 bulan']],
            ['health', 'in', 'range' => ['Sehat', 'Sakit']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eid' => 'EID',
            'vid' => 'Visual ID',
            'name' => 'Nama',
            'birthdate' => 'Tanggal Lahir',
            'cage_id' => 'Kandang',
            'type_of_livestock' => 'Jenis Ternak',
            'breed_of_livestock' => 'Ras Ternak',
            'purpose' => 'Tujuan Pemeliharaan',
            'maintenance' => 'Pola Pemeliharaan',
            'source' => 'Asal Ternak',
            'ownership_status' => 'Status Kepemilikan',
            'reproduction' => 'Kondisi Reproduksi',
            'gender' => 'Jenis Kelamin',
            'age' => 'Usia',
            'chest_size' => 'Lingkar Dada',
            'body_weight' => 'Berat Sapi',
            'health' => 'Kesehatan Ternak',
            'livestock_image' => 'Foto Ternak',
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
            'purpose',
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

        // $fields['chest_size'] = function ($model) {
        //     return $model->chest_size . ' cm';
        // };

        // $fields['body_weight'] = function ($model) {
        //     return $model->body_weight . ' kg';
        // };

        $fields['livestock_images'] = function ($model) {
            return array_map(function ($livestockImage) {
                return sprintf('https://storage.googleapis.com/digiternak1/%s', $livestockImage->image_path);
            }, $model->livestockImages);
        };

        $fields['created_at'] = 'created_at';
        $fields['updated_at'] = 'updated_at';

        return $fields;
    }

    public function validateBirthdate($attribute, $params)
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        $birthdate = \DateTime::createFromFormat('Y-m-d', $this->$attribute);
    
        $tomorrow = clone $today;
        $tomorrow->modify('+1 day');
    
        if ($birthdate >= $tomorrow) {
            $this->addError($attribute, 'Tanggal lahir ternak tidak boleh lebih dari hari ini.');
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
            $this->addError($attribute, 'Anda sudah memiliki ternak dengan nama yang sama. Silakan gunakan nama yang berbeda.');
        }
    }

    // Definisikan relasi dengan model LivestockImage
    public function getLivestockImages()
    {
        return $this->hasMany(LivestockImage::class, ['livestock_id' => 'id']);
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

    const SCENARIO_UPDATE = 'update';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['name', 'birthdate', 'cage_id', 'type_of_livestock', 'breed_of_livestock', 'maintenance', 'source', 'ownership_status', 'reproduction', 'gender', 'age', 'chest_size', 'body_weight'];
        return $scenarios;
    }
}
