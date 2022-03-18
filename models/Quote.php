<?php

namespace app\models;

use Yii;
use app\models\Product;
use app\models\Color;
/**
 * This is the model class for table "quote".
 *
 * @property int $id
 * @property int $order_number
 * @property string $created_at
 * @property string $updated_at
 * @property int $id_client
 * @property int $product
 * @property int $amount
 * @property int $color
 * @property int $packaging
 * @property int $placeholder
 * @property string|null $notes
 * @property float|null $total
 * @property float|null $deposit
 * @property float|null $balance
 * @property int $shipping
 * @property string $deadline
 * @property string $date_deposit
 * @property string $date_balance
 */
class Quote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_number', 'created_at', 'id_client',  'confirmed', 'shipping', 'deadline'], 'required'],
            [['order_number', 'id_client', 'confirmed', 'placeholder', 'shipping'], 'integer'],
            [['created_at', 'updated_at', 'deadline', 'product', 'amount', 'color', 'packaging',
                'date_deposit', 'date_balance','placeholder', 'address', 'custom_color', 'confetti', 'custom', 'custom_amount'], 'safe'],
            [['notes', 'address'], 'string'],
            [['total', 'deposit', 'balance'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_number'  => 'Numero preventivo',
            'created_at'    => 'Creato',
            'updated_at'    => 'Aggiornato',
            'id_client'     => 'Cliente',
            'product'       => 'Prodotto',
            'amount'        => 'Quantità',
            'color'         => 'Colore',
            'custom_color'  => "Colore custom",
            'packaging'     => 'Confezione',
            'placeholder'   => 'Segnaposto',
            'notes'         => 'Note',
            'total'         => 'Totale',
            'deposit'       => 'Acconto',
            'balance'       => 'Saldo',
            'shipping'      => 'Spedizione',
            'deadline'      => 'Consegna (entro il)',
            'confirmed'     => "Confermato",
            'address'       => "Indirizzo",
            'custom'        => "Personalizzazione",
            'custom_amount' => "Costo personalizzazione",
            'confetti'      => "Confetti",
            'invoice'       => "Fattura",
            'attachements'  => "Allegati"
        ];
    }

    public function getProduct(){
        $product = Product::findOne([$this->product]);
        return !empty($product) ? $product->name : "";
    }

    public function getColor(){
        $color = Color::findOne([$this->color]);
        return !empty($color) ? $color->label : "";
    }

    public function getClient(){
        $client = Client::findOne([$this->id_client]);
        return !empty($client) ? $client->name." ".$client->surname : "";
    }

    public function formatDate($value, $showHour = false){
        $format = "d/m/Y";
        if($showHour)
            $format = "d/m/Y H:i:s";

        return !empty($value) ? date($format, strtotime($value)) : "";
    }

    public function formatNumber($value){
        if(empty($value)) return;
        return number_format($value, 2, ",", ".")." &euro;";
    }
}
