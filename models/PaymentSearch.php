<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Payment;

/**
 * PaymentSearch represents the model behind the search form of `app\models\Payment`.
 */
class PaymentSearch extends Payment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_client', 'id_quote', 'payed', 'id_quote_placeholder'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'data_saldo'], 'safe'],
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
        $query = Payment::find();

        $query->leftJoin('quote', 'payment.id_quote=quote.id');

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
            'id_client' => $this->id_client,
            'id_quote' => $this->id_quote,
            'id_quote_placeholder' => $this->id_quote_placeholder,
            'payed' => $this->payed,
            'amount' => $this->amount,
        ]);

        $quotes = Quote::find()->where(["id" => $this->id_quote])->orderBy(["date_balance" => SORT_ASC])->all();
        $ids = [];
        foreach($quotes as $quote){
            $ids[$quote->id] = $quote->id;
        }
        
        
        if(!empty($params["PaymentSearch"]["start_date"]) || !empty($params["PaymentSearch"]["end_date"]))
        {
            $tmp_start_date = explode("/", $params["PaymentSearch"]["start_date"]);
            $start_date     = $tmp_start_date["2"]."-".$tmp_start_date["1"]."-".$tmp_start_date[0];
            $tmp_end_date   = explode("/", $params["PaymentSearch"]["end_date"]);
            $end_date       = $tmp_end_date["2"]."-".$tmp_end_date["1"]."-".$tmp_end_date[0];
            if($start_date == $end_date)
                $query->andFilterWhere(['LIKE', 'created_at', $start_date ]);
            else    
                $query->andFilterWhere(['>=', 'created_at', $start_date ])->andFilterWhere(['<=', 'created_at', $end_date]);
        }
        
        /** order by quote date_balance */
        $dataProvider->sort->attributes['data_saldo'] = [
            'asc' => ['quote.date_balance' => SORT_ASC],
        ];
    
        if(isset($params["sort"]) && $params["sort"] == "-data_saldo"){
            /** order by quote date_balance */
            $dataProvider->sort->attributes['data_saldo'] = [
                'desc' => ['quote.date_balance' => SORT_DESC],
            ];    
        }
        
        return $dataProvider;
    }
}
