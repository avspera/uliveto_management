<?php

namespace app\models;

use Yii;
use app\models\Product;

/**
 * This is the model class for table "color".
 *
 * @property int $id
 * @property int $id_product
 * @property string $label
 * @property string $content
 * @property string|null $picture
 */
class Color extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'color';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['label', 'id_product'], 'required'],
            [['content', 'picture'], 'safe'],
            [['label', 'content'], 'string', 'max' => 255],
            [['picture'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, png'],
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
            'content' => 'Codice hex',
            'picture' => 'Foto',
            'id_product' => "Prodotto"
        ];
    }

    public function getProduct(){
        $product = Product::find()->select(["name"])->where(["id" => $this->id_product])->one();
        return !empty($product) ? $product->name : "-";
    }
}
