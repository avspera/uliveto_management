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
 * @property string $created_at
 * @property string|null $updated_at
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
            [['id_quote', 'id_placeholder', 'amount'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
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

    public function formatDate($value, $showHour = false){
        $format = "d/m/Y";
        if($showHour)
            $format = "d/m/Y H:i:s";

        return !empty($value) ? date($format, strtotime($value)) : "";
    }
}
