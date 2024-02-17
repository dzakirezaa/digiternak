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
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields = parent::fields();

        // Hapus fields yang tidak perlu dari output JSON
        unset($fields['created_at'], $fields['updated_at']);

        return $fields;
    }
}
