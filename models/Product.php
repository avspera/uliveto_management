<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property string|null $image
 * @property string|null $weight
 * @property int $id_packaging
 * @property float $price
 * @property float $capacity
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'capacity'], 'required'],
            [['id_packaging'], 'integer'],
            [['id_packaging'], 'safe'],
            [['price', 'capacity'], 'number'],
            [['name', 'image', 'weight'], 'string', 'max' => 255],
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, png'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nome',
            'image' => 'Immagine',
            'weight' => 'Peso',
            'id_packaging' => 'Confezione',
            'price' => 'Prezzo',
            'capacity' => 'Capacità',
        ];
    }

    public function formatNumber($value){
        if(empty($value)) return;
        return number_format($value, 2, ",", ".")." &euro;";
    }
}
