<?php

namespace app\models;

use Yii;
use app\models\Product;
use app\models\Color;
use app\models\Sales;
use app\models\Segnaposto;
use app\models\QuotePlaceholder;
/**
 * This is the model class for table "quote".
 *
 * @property int $id
 * @property int $order_number
 * @property string $created_at
 * @property string $updated_at
 * @property string $data_eventou
 * @property int $id_client
 * @property int $delivered
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
 * @property int $from_web
 * @property string $deadline
 * @property string $scandenza_offerta
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
            [['order_number', 'created_at', 'id_client',  'confirmed', 'shipping'], 'required'],
            [['order_number', 'id_client', 'confirmed', 'placeholder', 'shipping', 'id_sconto'], 'integer'],
            [['created_at', 'updated_at', 'data_evento', 'deadline', 'product', 'amount', 'color', 'packaging', 'total_no_vat',
                'date_deposit', 'date_balance','placeholder', 'address', 'custom_color', 'placeholder_amount',
                    'confetti', 'custom', 'custom_amount', 'id_sconto', 'custom_amount_omaggio',
                    'prezzo_confetti', 'confetti_omaggio', 'delivered', 'scadenza_offerta', 'deadline', 'from_web'], 'safe'],
            [['notes', 'address'], 'string'],
            [['total', 'deposit', 'balance', 'delivered'], 'number'],
            [['attachments'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, png, pdf'],

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
            'data_evento'   => 'Data Evento',
            'id_client'     => 'Cliente',
            'product'       => 'Prodotto',
            'amount'        => 'Quantità',
            'color'         => 'Colore',
            'custom_color'  => "Colore personalizzato",
            'packaging'     => 'Confezione',
            'placeholder'   => 'Segnaposto',
            'notes'         => 'Note',
            'total'         => 'Totale',
            'total_no_vat'   => 'Totale senza iva',
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
            'attachments'   => "Allegati",
            'id_sconto'     => "Sconto",
            'delivered'     => "Consegnato",
            'custom_amount_omaggio' => "Personalizzazione omaggio",
            'scadenza_offerta' => "Scadenza offerta",
            'date_deposit' => "Data acconto",
            'date_balance' => "Data saldo",
            'from_web' => "Da web"
        ];
    }

    public function getProduct(){
        $product = Product::findOne([$this->product]);
        return !empty($product) ? $product->name : "";
    }

    public function getProducts(){
        $details = QuoteDetails::findAll(["id_quote" => $this->id]);

        $html = "";
        $i = 0;
        foreach($details as $detail){
            $html .= $detail->getProduct();
            if($i < count($details) - 1 ){
                $html .= " - ";
            }
            $i++;
        }

        return $html;
    }

    public function getColor(){
        $color = Color::findOne([$this->color]);
        return !empty($color) ? $color->label : "";
    }

    public function getClient(){
        $client = Client::findOne([$this->id_client]);
        return !empty($client) ? $client->name." ".$client->surname : "";
    }

    public function getSegnaposto(){
        $segnaposto = QuotePlaceholder::findOne(["id_quote" => $this->id]);
        return $segnaposto;
    }    

    public function getSegnapostoTotale(){
        $quotePlaceholder   = QuotePlaceholder::find()->where(["id_quote" => $this->id])->one();
        $placeholder        = Segnaposto::findOne($quotePlaceholder->id_placeholder);
        $total              = $quotePlaceholder->amount * floatval($placeholder->price);
        return $this->formatNumber($total);
    }
    public function getClientPhone(){
        $client = Client::findOne([$this->id_client]);
        return !empty($client) ? $client->phone : "";
    }

    public function getSale(){
        $sale = Sales::findOne([$this->id_sconto]);
        return !empty($sale) ? $sale->name." ".$sale->formatPercentage($sale->amount) : " - ";
    }

    public function calculatePercentage($value, $total, $formatted = false){
        $amount = 0;
        $amount = ($value / 100) * $total;
        $amount = is_numeric($amount) ? floatval($amount) : 0;

        return $formatted ? $this->formatNumber($amount) : $amount;
    }

    public function getTotalAmount(){
        $amount = QuoteDetails::find()->where(["id_quote" => $this->id])->sum("amount");
        return $amount;
    }

    public function getPlaceholder(){
        $placeholder = Segnaposto::findOne([$this->placeholder]);
        return !empty($placeholder) ? $placeholder->label : "";
    }

    public function getPlaceholderTotal(){
        $placeholder = Segnaposto::find()->select(["price"])->where(["id" => $this->placeholder])->one();
        if(empty($placeholder)) return "-";
        $sumProducts = QuoteDetails::find()->where(["id_quote" => $this->id])->sum("amount");
        return !empty($placeholder) ? $this->formatNumber($placeholder->price*$sumProducts) : 0;
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
