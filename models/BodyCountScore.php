<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class BodyCountScore extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%bcs}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['livestock_id', 'body_weight', 'chest_size', 'hips', 'date_check'], 'required',  'message' => '{attribute} tidak boleh kosong.'],
            [['livestock_id'], 'integer'],
            [['body_weight', 'chest_size', 'hips'], 'number', 'min' => 0, 'tooSmall' => '{attribute} harus bernilai positif.', 'message' => '{attribute} harus berupa angka.', 'skipOnEmpty' => true],
            [['date_check'], 'safe'],
            [['bcs_image'], 'string'],
            [['bcs_image'], 'file', 'extensions' => ['png', 'jpg', 'jpeg'], 'maxSize' => 1024 * 1024 * 5, 'maxFiles' => 5, 'message' => 'Format file tidak valid atau ukuran file terlalu besar (maksimal 5 MB).'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'livestock_id' => 'ID Ternak',
            'body_weight' => 'Berat Sapi',
            'chest_size' => 'Lingkar Dada',
            'hips' => 'Ukuran Pinggul',
            'date_check' => 'Tanggal Pemeriksaan',
            'bcs_image' => 'Dokumentasi Pemeriksaan',
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'livestock_id',
            'body_weight',
            'chest_size',
            'hips',
            'date_check',
        ];

        $fields['bcs_images'] = function ($model) {
            return array_map(function ($bcsImage) {
                return sprintf('https://storage.googleapis.com/digiternak1/%s', $bcsImage->image_path);
            }, $model->bcsImages);
        };

        return $fields;
    }

    public function getBcsImages()
    {
        return $this->hasMany(BcsImage::class, ['bcs_id' => 'id']);
    }
}