<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\QuotePlaceholder;

/**
 * QuotePlaceholderSearch represents the model behind the search form of `app\models\Product`.
 */
class QuotePlaceholderSearch extends QuotePlaceholder
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_quote', 'amount', 'id_placeholder', 'id_quote', 'confirmed'], 'integer'],
            [['date_deposit', 'date_balance'], 'string'],
            [['created_at', "updated_at", "saldo", "acconto"], 'safe']
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
        $query = QuotePlaceholder::find();

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
            'id_quote' => $this->id_quote,
            'id_placeholder' => $this->id_placeholder,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'amount'    => $this->amount,
            'saldo'     => $this->saldo,
            'acconto'    => $this->acconto,
            'confirmed' => $this->confirmed,
        ]);

        $query->andFilterWhere(['like', 'date_deposit', $this->date_deposit])
            ->andFilterWhere(['like', 'date_balance', $this->date_balance]);


        return $dataProvider;
    }
}
