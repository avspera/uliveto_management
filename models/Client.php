<?php

namespace app\models;

use Yii;
use app\models\Occurrence;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $phone
 * @property string $age
 * @property int $occurrence
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'email', 'phone', 'age', 'occurrence'], 'required'],
            [['occurrence'], 'integer'],
            [['name', 'surname', 'email', 'phone', 'age'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nome',
            'surname' => 'Cognome',
            'email' => 'Email',
            'phone' => 'Telefono',
            'age' => 'Età',
            'occurrence' => 'Occorrenza',
        ];
    }

    public function getOccorrence(){
        $result = Occorrence::findOne([$this->occurrence]);
        return !empty($result) ? $result->label : "";
    }
}
