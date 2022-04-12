<?php

use yii\helpers\Html;

use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
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
                        'attribute' => "total",
                        'value' => function($model){
                            return $model->formatNumber($model->getTotal());
                        },
                        'format' => "raw",
                        'label' => "Totale ordine"
                    ],
                    [
                        'attribute' => 'amount',
                        'value' => function($model){
                            return $model->formatNumber($model->amount);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => "fatturato",
                        'value' => function($model){
                            return $model->isFatturato();
                        },
                        'filter' => [0 => "NO", 1 => "SI"]
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        },
                        'label' => "Data pagamento"
                    ],
                    [
                        'attribute' => "saldo",
                        'value' => function($model){
                            $totale = $model->getTotal();
                            if(!$totale) return;
                            return $totale - $model->amount < 0 ? 0 : $model->formatNumber($totale - $model->amount);
                        },
                        'format' => "raw",
                        'label' => "Saldo"
                    ],
                    [
                        'attribute' => "type",
                        'value' => function($model){
                            return $model->getType();
                        }
                    ],
                    [
                        'attribute' => "data_saldo",
                        'value' => function($model){
                            // $date = new DateTime($model->created_at);
                            // $date->add(new DateInterval('P10D'));
                            $quote = Quote::findOne($model->id_quote);
                            return $model->formatDate($quote->date_balance);
                        },
                        'format' => "raw",
                        'label' => "Data Saldo"
                    ],
                    [
                        'attribute' => "fatturato",
                        'value' => function($model){
                            return $model->fatturato ? "<i class='fas fa-check' style='color:green'></i>" : "<i class='fas fa-ban' style='color:red'></i>";
                        },
                        'filter' => [0 => "NO", 1 => "SI"],
                        'format' => "raw"
                    ],
                    [ 'class' => ActionColumn::className() ],
                ],
            ]); ?>
        </div>
    </div>

    <?php 
        // Modal::begin([
        //     'header' => '<h3>Crear Evento</h3>',
        //     'id'=>'create',
        //     'size'=>'modal-lg',
        // ]);

        // echo "<div id='modalCreate'></div>";
        // Modal::end();
    ?>
</div>


<script>
    function openModal(){
        console.log("stocazzo")
    }
</script>
