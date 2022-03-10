<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property string $label
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
            [['name', 'label', 'id_packaging', 'price', 'capacity'], 'required'],
            [['id_packaging'], 'integer'],
            [['price', 'capacity'], 'number'],
            [['name', 'label', 'image', 'weight'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'label' => 'Label',
            'image' => 'Image',
            'weight' => 'Weight',
            'id_packaging' => 'Id Packaging',
            'price' => 'Price',
            'capacity' => 'Capacity',
        ];
    }
}
