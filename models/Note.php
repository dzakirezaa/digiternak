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
            [['livestock_vid', 'date_recorded', 'details'], 'required'],
            [['date_recorded', 'created_at', 'updated_at'], 'safe'],
            [['date_recorded'], 'date', 'format' => 'php:Y-m-d'],
            [['details'], 'string'],
            [['livestock_vid'], 'string', 'max' => 255],
            [['documentation_path'], 'string', 'max' => 255],
            ['livestock_vid', 'validateLivestockVid'],
            [['date_recorded'], 'date', 'format' => 'php:Y-m-d', 'message' => 'Format tanggal tidak valid. Gunakan format yyyy-mm-dd.'],
            [['date_recorded'], 'compare', 'compareValue' => date('Y-m-d'), 'operator' => '<=', 'message' => 'Tanggal harus hari ini atau sebelum hari ini.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'livestock_vid' => 'Livestock VID',
            'date_recorded' => 'Date Recorded',
            'details' => 'Details',
            'documentation_path' => 'Documentation Path',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function validateLivestockVid($attribute, $params)
    {
        $livestock = Livestock::findOne(['vid' => $this->$attribute]);

        if ($livestock === null) {
            $this->addError($attribute, 'Livestock with specified VID not found.');
        }
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['date_recorded'] = function ($model) {
            return Yii::$app->formatter->asDate($model->date_recorded, 'php:d-m-Y');
        };
        return $fields;
    }

}
