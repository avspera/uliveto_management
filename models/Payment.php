<?php

namespace app\models;

use Yii;
use app\models\Client;
use app\models\Quote;
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
            [['id_client', 'id_quote', 'amount', 'created_at', 'fatturato'], 'required'],
            [['id_client', 'id_quote', 'fatturato'], 'integer'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_client' => 'Cliente',
            'id_quote' => 'Preventivo',
            'amount' => 'Acconto',
            'created_at' => 'Effettuato il',
            'fatturato' => "Fatturato"
        ];
    }


    public function getQuote(){
        $out = ["quote" => "", "confirmed" => 0];
        $quote = Quote::findOne([$this->id_quote]);
        if(!empty($quote)){
            $out["quote"]       = $quote->id." - ".$this->formatDate($quote->created_at);
            $out["confirmed"]   = $quote->confirmed;
        }
        return $out;
    }

    public function isFatturato(){
        return $this->fatturato ? "SI" : "NO";
    }
    
    public function getTotal(){
        $quote = Quote::find()->select(["total"])->where(["id" => $this->id_quote])->one();
        return !empty($quote) ? $quote->total : "";
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
