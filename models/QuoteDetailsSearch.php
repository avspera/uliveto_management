<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\QuoteDetails;

/**
 * QuoteDetailsSearch represents the model behind the search form of `app\models\QuoteDetails`.
 */
class QuoteDetailsSearch extends QuoteDetails
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_quote', 'id_product', 'amount', 'id_packaging', 'id_color'], 'integer'],
            [['created_at', 'custom_color'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = QuoteDetails::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id'            => $this->id,
            'id_quote'      => $this->id_quote,
            'id_product'    => $this->id_product,
            'amount'        => $this->amount,
            'id_packaging'  => $this->id_packaging,
            'created_at'    => $this->created_at,
            'custom_color'  => $this->custom_color,
        ]);

        return $dataProvider;
    }
}
