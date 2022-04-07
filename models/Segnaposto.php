<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "segnaposto".
 *
 * @property int $id
 * @property string $label
 * @property string|null $image
 * @property float|null $price
 * @property string|null $created_at
 */
class Segnaposto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'segnaposto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['label', 'created_at'], 'required'],
            [['price'], 'number'],
            [['image'], 'safe'],
            [['label', 'image'], 'string', 'max' => 255],
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
            'label' => 'Nome',
            'image' => 'Immagine',
            'price' => 'Prezzo',
            'created_at' => 'Aggiunto il',
        ];
    }

    public function formatDate($value, $showHour = false){
        $format = "d/m/Y";
        if($showHour)
            $format = "d/m/Y H:i:s";

        return !empty($value) ? date($format, strtotime($value)) : "";
    }

    public function getTotal($quoteId){
        
        $amount = QuoteDetails::find()->where(["id_quote" => $quoteId])->sum("amount");
        $amount = is_numeric($amount) ? intVal($amount) : 0;

        return $this->formatNumber($model->price * $amount);
    }

    public function formatNumber($value){
        if(empty($value)) return;
        return number_format($value, 2, ",", ".")." &euro;";
    }
}
