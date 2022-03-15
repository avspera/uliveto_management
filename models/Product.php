<?php

namespace app\models;

use Yii;
use app\models\Packaging;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property string|null $image
 * @property string|null $weight
 * @property int $id_packaging
 * @property float $price
 * @property string $capacity
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
            [['id_packaging', 'weight', 'image'], 'safe'],
            [['price', ], 'number'],
            [['name', 'image', 'weight'], 'string', 'max' => 255],
            [['capacity'], 'string', 'max' => 10],
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

    public function getPackaging(){
        $packaging = Packaging::findOne(["id" => $this->id_packaging]);
        
        return !empty($packaging) ? $packaging->label : "";
    }
}
