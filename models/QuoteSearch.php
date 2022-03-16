<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Quote;

/**
 * QuoteSearch represents the model behind the search form of `app\models\Quote`.
 */
class QuoteSearch extends Quote
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_number', 'id_client', 'confirmed',
                'product', 'amount', 'color', 'packaging', 'placeholder', 'shipping'], 'integer'],
            [['created_at', 'updated_at', 'notes', 'deadline'], 'safe'],
            [['total', 'deposit', 'balance'], 'number'],
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
        $query = Quote::find();

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
            'id' => $this->id,
            'order_number' => $this->order_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id_client' => $this->id_client,
            'product' => $this->product,
            'amount' => $this->amount,
            'color' => $this->color,
            'packaging' => $this->packaging,
            'placeholder' => $this->placeholder,
            'total' => $this->total,
            'deposit' => $this->deposit,
            'balance' => $this->balance,
            'shipping' => $this->shipping,
            'deadline' => $this->deadline,
            'confirmed' => $this->confirmed,
        ]);

        $query->andFilterWhere(['like', 'notes', $this->notes]);

        return $dataProvider;
    }
}
