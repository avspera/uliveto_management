<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pagamenti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <div class="card">
            <div class="card-header">
                <?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?>
            </div>

            <div class="card-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
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
                                return $model->getQuote();
                            }
                        ],
                        [
                            'attribute' => "total",
                            'value' => function($model){
                                return $model->formatNumber($model->getTotal());
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
                            'attribute' => "saldo",
                            'value' => function($model){
                                $totale = $model->getTotal();
                                return $model->formatNumber($totale - $model->amount);
                            },
                            'format' => "raw",
                            'label' => "Saldo"
                        ],
                        [
                            'attribute' => 'created_at',
                            'value' => function($model){
                                return $model->formatDate($model->created_at);
                            }
                        ],
                        [
                            'class' => ActionColumn::className(),
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   


</div>
