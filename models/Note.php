<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Note extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%note}}';
    }

    public function rules()
    {
        return [
            [['livestock_vid', 'livestock_cage', 'date_recorded', 'location', 'livestock_feed', 'costs'], 'required'],
            [['date_recorded'], 'date', 'format' => 'php:Y-m-d'],
            [['date_recorded'], 'compare', 'compareValue' => date('Y-m-d'), 'operator' => '<=', 'message' => 'Tanggal harus hari ini atau sebelum hari ini.'],
            [['livestock_vid', 'livestock_cage'], 'string', 'max' => 10],
            [['location', 'livestock_feed'], 'string', 'max' => 255],
            [['details'], 'string'],
            [['costs'], 'string', 'max' => 20],
            [['documentation'], 'file', 'maxFiles' => 5, 'maxSize' => 1024 * 1024 * 5], // Maksimum 5 file, 5 MB per file
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'livestock_vid' => 'Livestock VID',
            'livestock_cage' => 'Livestock Cage',
            'date_recorded' => 'Date Recorded',
            'location' => 'Location',
            'livestock_feed' => 'Livestock Feed',
            'details' => 'Details',
            'costs' => 'Costs',
            'documentation' => 'Documentation',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    // public function fields()
    // {
    //     $fields = parent::fields();
    //     $fields['date_recorded'] = function ($model) {
    //         return Yii::$app->formatter->asDate($model->date_recorded, 'php:Y-m-d');
    //     };
    //     return $fields;
    // }
}
