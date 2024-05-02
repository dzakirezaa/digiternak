<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Note extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%note}}';
    }

    public function rules()
    {
        return [
            [['livestock_feed', 'costs', 'details'], 'required', 'message' => '{attribute} cannot be blank'],
            ['costs', 'validateCosts'],
            // [['date_recorded'], 'date', 'format' => 'php:d F Y', 'message' => 'Invalid date format for {attribute}. Please use the d F Y format'],
            // [['date_recorded'], 'validateDateFormat'],
            // [['livestock_vid'], 'match', 'pattern' => '/^[A-Z]{3}\d{4}$/', 'message' => '{attribute} must follow the pattern of three uppercase letters followed by four digits'],
            // [['livestock_cage'], 'match', 'pattern' => '/^[A-Za-z0-9\s]{3,10}$/', 'message' => '{attribute} must be between 3 and 10 characters long and may contain letters, numbers, and spaces only'],
            [['location', 'livestock_feed', 'details'], 'string', 'max' => 255],
            [['documentation'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10, 'extensions' => ['jpg', 'jpeg', 'png'] , 'maxSize' => 1024 * 1024 * 10, 'message' => 'Invalid file format or file size exceeded (maximum 10 MB)'],
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'livestock_id',
            'livestock_vid',
            'livestock_cage',
            'date_recorded',
            'location',
            'livestock_feed',
            'costs',
            'details',
        ];

        $fields['note_images'] = function ($model) {
            return array_map(function ($noteImage) {
                return $noteImage->image_path;
            }, $model->noteImages);
        };

        return $fields;
    }

    public function attributeLabels()
    {
        return [
            'livestock_id' => 'Livestock ID',
            'livestock_vid' => 'Visual ID',
            'livestock_cage' => 'Cage',
            'date_recorded' => 'Date Recorded',
            'location' => 'Location',
            'livestock_feed' => 'Livestock Feed',
            'costs' => 'Costs',
            'details' => 'Details',
            'documentation' => 'Documentation',
        ];
    }

    public function validateCosts($attribute, $params)
    {
        $costs = Yii::$app->getRequest()->getBodyParams()['costs'];

        if (is_float($costs)) {
            $this->addError($attribute, 'Costs must be an integer.');
        } elseif (!preg_match('/^\d+$/', $this->$attribute)) {
            $this->addError($attribute, 'Costs must be an integer.');
        }
    }

    public function validateDateFormat($attribute, $params)
    {
        // Ambil nilai tanggal dari atribut model
        $date_recorded = $this->$attribute;

        // Setel zona waktu menjadi 'Asia/Jakarta'
        date_default_timezone_set('Asia/Jakarta');

        // Konversi tanggal input ke objek DateTime
        $date = \DateTime::createFromFormat('d F Y', $date_recorded);

        // Cek apakah tanggal berhasil di-parse dan tidak melebihi tanggal hari ini
        if ($date && $date <= new \DateTime('today')) {
            // Konversi format tanggal ke Y-m-d
            $this->$attribute = $date->format('Y-m-d');
        } else {
            // Tanggal tidak valid atau melebihi tanggal hari ini
            $this->addError($attribute, 'Date recorded must be today or before today');
        }
    }

    // Definisikan relasi dengan model NoteImage
    public function getNoteImages()
    {
        return $this->hasMany(NoteImage::class, ['note_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Get user_id from the currently logged in user
        $userId = Yii::$app->user->identity->id;

        // Save user_id
        $this->updateAttributes(['user_id' => $userId]);
    }
}
