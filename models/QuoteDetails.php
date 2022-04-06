<?php

namespace app\models;

use Yii;
use app\models\Product;
use app\models\Color;
use app\models\Packaging;
/**
 * This is the model class for table "quote_details".
 *
 * @property int $id
 * @property int $id_quote
 * @property int $id_product
 * @property int $amount
 * @property int $id_packaging
 */
class QuoteDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_quote', 'id_product', 'amount', 'created_at'], 'required'],
            [['id_quote', 'id_product', 'amount', 'id_packaging', 'id_color'], 'integer'],
            [['id_packaging', 'custom_color'], 'safe'],
            [['created_at'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_quote' => 'Preventivo',
            'id_product' => 'Prodotto',
            'id_color' => "Colore",
            'amount' => 'Quantità',
            'id_packaging' => 'Scatola',
            'custom_color' => "Altro colore"
        ];
    }

    public function getProduct(){
        $product = Product::findOne([$this->id_product]);
        return !empty($product) ? $product->name : "";
    }

    public function getQuoteInfo(){
        $quote = Quote::findOne([$this->id_quote]);
        return !empty($quote) ? $quote->order_number." - ".$quote->getClient() : "";
    }

    public function getColor(){
        $color = Color::findOne([$this->id_color]);
        return !empty($color) ? $color->label : "";
    }

    public function formatNumber($value){
        if(empty($value)) return;
        return number_format($value, 2, ",", ".")." &euro;";
    }

    public function getPackaging(){
        $packaging = Packaging::findOne(["id" => $this->id_packaging]);
        
        return !empty($packaging) ? $packaging->label : "";
    }

    public function formatDate($value, $showHour = false){
        $format = "d/m/Y";
        if($showHour)
            $format = "d/m/Y H:i:s";

        return !empty($value) ? date($format, strtotime($value)) : "";
    }
}
