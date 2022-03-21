<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invia_catalogo".
 *
 * @property int $id
 * @property string $email
 * @property string|null $name
 * @property string|null $telefono
 * @property string|null $created_at
 */
class InviaCatalogo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invia_catalogo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'created_at'], 'required'],
            [['email', 'name', 'telefono', 'created_at'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'name' => 'Name',
            'telefono' => 'Telefono',
            'created_at' => "Inviato il"
        ];
    }

    public function formatDate($value, $showHour = false){
        $format = "d/m/Y";
        if($showHour)
            $format = "d/m/Y H:i:s";

        return !empty($value) ? date($format, strtotime($value)) : "";
    }
}
