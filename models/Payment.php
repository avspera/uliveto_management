<?php

namespace app\models;

use Yii;
use app\models\Client;
use app\models\Quote;
use app\models\QuotePlaceholder;
/**
 * This is the model class for table "payments".
 *
 * @property int $id
 * @property int $id_client
 * @property int $id_quote
 * @property float $amount
 * @property string $created_at
 */
class Payment extends \yii\db\ActiveRecord
{
    public $total;
    public $saldo;
    public $types = [0 => "Acconto", 1 => "Saldo"];
    public $types_reverse = ["Acconto" => 0, "Saldo" => 1];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_client', 'amount', 'created_at', 'fatturato', 'type', 'payed'], 'required'],
            [['id_client', 'id_quote', 'id_quote_placeholder', 'fatturato', 'type', 'payed'], 'integer'],
            [['amount'], 'number'],
            [['id_transaction', 'id_quote', 'id_quote_placeholder', 'allegato'], 'safe'],
            [['allegato'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, pdf, png'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'id_client'     => 'Cliente',
            'id_quote'      => 'Ordine Bomboniere',
            'id_quote_placeholder' => 'Ordine Segnaposto',
            'amount'        => 'Quantità',
            'created_at'    => 'Effettuato il',
            'fatturato'     => "Fatturato",
            'type'          => "Tipo",
            'payed'         => "Versato",
            
        ];
    }

    public function getType(){
        return $this->types[$this->type];
    }

    public function getQuotePlaceholder(){
        $out = ["quote" => "", "confirmed" => 0];
        $quote = QuotePlaceholder::find()->select(["id", "created_at"])->where(["id" => $this->id_quote_placeholder])->one();

        if(!empty($quote)){
            $out["quote"]       = $quote->id." - ".$this->formatDate($quote->created_at);
        }
        
        return $out;
    }

    public function getQuote(){
        $out = ["quote" => "", "confirmed" => 0];
        $quote = Quote::find()->select(["order_number", "created_at", "confirmed"])->where(["id" => $this->id_quote])->one();

        if(!empty($quote)){
            $out["quote"]       = $quote->order_number." - ".$this->formatDate($quote->created_at);
            $out["confirmed"]   = $quote->confirmed;
        }

        return $out;
    }

    public function isFatturato(){
        return $this->fatturato ? "SI" : "NO";
    }

    public function isPayed(){
        return $this->payed ? "SI" : "NO";
    }
    
    public function getTotal(){
        $total = 0;
        $quote = Quote::find()->select(["total"])->where(["id" => $this->id_quote])->one();
        
        if(empty($quote)){
            $quote = QuotePlaceholder::find()->where(["id" => $this->id_quote_placeholder])->one();
            if(!empty($quote))
                $total = $quote->getTotal();
        }
        else{
            $total = $quote->total;
        }

        return $total;
    }

    public function checkPayments(){
        if(!empty($this->id_quote))
            $pagamenti = Payment::findAll(["id_quote" => $this->id_quote]);
        else if(!empty($this->id_quote_placeholder)){
            $pagamenti = Payment::findAll(["id_quote_placeholder" => $this->id_quote_placeholder]);
        }else{
            $pagamenti = [];
        }
        
        return count($pagamenti);
    }

    public function getSaldo(){
        if(!empty($model->id_quote))
            $pagamenti = Payment::find()->where(["id_quote" => $this->id_quote])->all();
        else{
            $pagamenti = Payment::findAll(["id_quote" => $this->id_quote_placeholder]);
        }
        
        $acconto = 0;
        foreach($pagamenti as $payment){
            if($payment->type == 0){
                $acconto = $payment->amount;
            }
        }

        $totale = $this->getTotal();
        return $acconto;
        return ($totale-$acconto);
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

    public function isSaldato($key){

        $payment = Payment::find()
                    ->where(["id_client" => $this->id_client])
                    ->andWhere([$key => $this->$key])
                    ->andWhere(['not', ['id_transaction' => null]])
                    ->one();
        
        return empty($payment) ? "SI" : "NO";
    }

    public function formatNumber($value){
        if(empty($value)) return;
        return number_format($value, 2, ",", ".")." &euro;";
    }
}
