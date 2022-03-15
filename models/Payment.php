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
            [['id_client', 'id_quote', 'amount', 'created_at'], 'required'],
            [['id_client', 'id_quote'], 'integer'],
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
            'amount' => 'Importo',
            'created_at' => 'Effettuato il',
        ];
    }


    public function getQuote(){
        $quote = Quote::findOne([$this->id_quote]);
        return !empty($quote) ? $quote->id." - ".$this->formatDate($quote->created_at) : "";
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
