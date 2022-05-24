<?php

namespace app\models;

use Yii;
use app\models\Quote;
/**
 * This is the model class for table "quote_placeholder".
 *
 * @property int $id
 * @property int $id_quote
 * @property int $id_placeholder
 * @property int $amount
 * @property float $saldo
 * @property float $acconto
 * @property string $created_at
 * @property string|null $date_deposit
 * @property string|null $date_balance
 */
class QuotePlaceholder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_placeholder';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_quote', 'id_placeholder', 'amount', 'created_at'], 'required'],
            [['id_quote', 'id_placeholder', 'amount', 'confirmed'], 'integer'],
            [['date_deposit', 'date_balance', 'created_at', 'updated_at'], 'string'],
            [['created_at', 'updated_at', 'date_deposit', "date_balance","saldo", "acconto"], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_quote' => 'Preventivo/ordine',
            'id_placeholder' => 'Segnaposto',
            'amount' => 'Quantità',
            'created_at' => 'Creato il',
            'updated_at' => 'Aggiornato il',
            'date_deposit' => "Data acconto",
            'date_balance' => "Data saldo",
            'confirmed' => "Confermato"
        ];
    }

    public function getQuoteInfo(){
        $quote = Quote::findOne([$this->id_quote]);
        return !empty($quote) ? $quote->order_number." - ".$quote->getClient() : "";
    }

    public function getPlaceholderInfo(){
        $segnaposto = Segnaposto::findOne([$this->id_placeholder]);
        return !empty($segnaposto) ? $segnaposto->label." - ".$segnaposto->formatNumber($segnaposto->price) : "";
    }

    public function getTotal($flag = "vat"){
        
        $placeholder = Segnaposto::findOne(["id" => $this->id_placeholder]);
        $price = !empty($placeholder) ? floatval($placeholder->price) : 0;
        $totalNoVat = intval($this->amount) * floatval($price);

        if($flag == "no_vat")
            return $this->formatNumber($totalNoVat);
        else{
            $subtotal = $this->amount * floatval($price);
            $totaleWithVat = ($subtotal + ($subtotal / 100) * 22);
            $total = is_numeric($totaleWithVat) ? $this->formatNumber($totaleWithVat) : 0;
          
            return $total;
        }
    }

    public function formatNumber($value){
        if(empty($value)) return;
        return number_format($value, 2, ",", ".")." &euro;";
    }
    
    public function formatDate($value, $showHour = false){
        $format = "d/m/Y";
        if($showHour)
            $format = "d/m/Y H:i:s";

        return !empty($value) ? date($format, strtotime($value)) : "";
    }
}
