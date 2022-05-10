<?php

namespace app\models;

use Yii;
use app\models\Product;

/**
 * This is the model class for table "packaging".
 *
 * @property int $id
 * @property int $id_product
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
            [['name', 'label', 'created_at', 'id_product'], 'required'],
            [['price'], 'safe'],
            [['name', 'label', 'image'], 'string', 'max' => 255],
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
            'name' => 'Codice',
            'label' => 'Nome pubblico',
            'image' => 'Immagine',
            'price' => "Prezzo",
            'created_at' => "Creato il",
            'id_product' => "Prodotto"
        ];
    }

    public function formatNumber($value){
        if(empty($value)) return;
        return number_format($value, 2, ",", ".")." &euro;";
    }

    public function getProduct(){
        $product = Product::findOne(["id" => $this->id_product]);
        return !empty($product) ? $product->name : "-";
    }
}
