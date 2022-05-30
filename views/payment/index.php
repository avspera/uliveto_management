<?php

use yii\helpers\Html;

use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use app\models\QuotePlaceholder;
use app\models\Quote;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pagamenti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    
    <div class="card">
        <div class="card-header">
            <?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?>
        </div>

        <div class="card-body table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'id_client',
                        'value' => function($model){
                            return $model->getClient();
                        }
                    ],
                    [
                        'attribute' => 'id_quote',
                        'value' => function($model){
                            $quote = $model->getQuote();
                            return Html::a($quote["quote"], Url::to([$quote["confirmed"] ? "order/view" : "quotes/view", "id" => $model->id_quote]));
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'id_quote_placeholder',
                        'value' => function($model){
                            $quote = $model->getQuotePlaceholder();
                            return Html::a($quote["quote"], Url::to(["quote-placeholder/view", "id" => $model->id_quote_placeholder]));
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => "total",
                        'value' => function($model){
                            return $model->getTotal();
                        },
                        'format' => "raw",
                        'label' => "Totale"
                    ],
                    [
                        'attribute' => 'amount',
                        'value' => function($model){
                            return $model->formatNumber($model->amount);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => "type",
                        'value' => function($model){
                            return $model->getType();
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        },
                        'label' => "Data pagamento"
                    ],
                    [
                        'attribute' => "payed",
                        'value' => function($model){
                            return $model->isPayed();
                        },
                        'filter' => [0 => "NO", 1 => "SI"],
                        'format' => "raw"
                    ],
                    [
                        'attribute' => "fatturato",
                        'value' => function($model){
                            return $model->isFatturato();
                        },
                        'filter' => [0 => "NO", 1 => "SI"],
                        'format' => "raw"
                    ],
                    [
                        'attribute' => "saldo",
                        'value' => function($model){
                            $pagamenti = $model->checkPayments();
                            
                            if($pagamenti == 2){
                                return 0;
                            }else if ($pagamenti == 1){
                                $saldo = $model->getSaldo();
                                return $model->formatNumber($saldo);
                            }else{
                                $totale = $model->getTotal();
                                return $model->formatNumber($totale);
                            }
                        },
                        'format' => "raw",
                        'label' => "Resta da saldare"
                    ],
                    [
                        'attribute' => "data_saldo",
                        'value' => function($model){
                            $date = "";
                            if(!empty($model->id_quote)){
                                $quote = Quote::findOne($model->id_quote);
                                $date = $model->formatDate($quote->date_balance);
                            }else if(!empty($model->id_quote_placeholder)){
                                $quotePlacelhoder = QuotePlaceholder::findOne(["id" => $model->id_quote_placeholder]);
                                $date = !empty($quotePlacelhoder) ? $quotePlacelhoder->formatDate($quotePlacelhoder->date_balance) : "-";
                            }else{
                                $date = "-";
                            }
                            return $date;
                        },
                        'format' => "raw",
                        'label' => "Data Saldo"
                    ],
                    [
                        'attribute' => "allegato",
                        'value' => function($model){
                            if(!empty($model->allegato)){
                                $allegati = json_decode($model->allegato, true);
                                if(!empty($allegati)){
                                    foreach($allegati as $allegato){
                                        return Html::a("Ricevuta pagamento", Yii::getAlias("@web")."/".Url::to($allegato));
                                    }
                                }
                                
                            }else{
                                return "-";
                            }
                        },
                        'format' => "raw"
                    ],
                    // [
                    //     'attribute' => "external_payment",
                    //     'value' => function($model){
                    //         return Html::a("Qui", Url::to(["external-payment", "id_client" => $model->id_client, "id_quote" => $model->id_quote]), []);
                    //     },
                    //     'format' => "raw"
                    // ],
                    [ 'class' => ActionColumn::className() ],
                ],
            ]); ?>
        </div>
    </div>

</div>
