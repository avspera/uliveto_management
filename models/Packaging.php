<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "packaging".
 *
 * @property int $id
 * @property string $name
 * @property string $label
 * @property string|null $image
 */
class Packaging extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'packaging';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'label'], 'required'],
            [['name', 'label', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Codice',
            'label' => 'Nome pubblico',
            'image' => 'Immagine',
        ];
    }
}
