<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Gender extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%gender}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gender_name'], 'required'],
            [['gender_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields = parent::fields();

        // Hapus fields yang tidak perlu dari output JSON
        unset($fields['created_at'], $fields['updated_at'], $fields['is_deleted'], $fields['id']);

        return $fields;
    }
}
