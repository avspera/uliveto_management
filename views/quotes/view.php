<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $model app\models\Quote */
$client = $model->getClient();
$this->title = $model->order_number." - ".$client;
$this->params['breadcrumbs'][] = ['label' => 'Preventivi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-view">

    <div class="card">
        <div class="card-header">
            <?= Html::a('<i class="fas fa-pencil-alt"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Cancella', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Sei sicuro di voler cancellare questo elemento?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('<i class="fas fa-file-pdf"></i> Genera PDF', ['generate-pdf', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-check"></i> Conferma', ['confirm', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        </div>

        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'order_number',
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function($model){
                            return $model->formatDate($model->updated_at);
                        }
                    ],
                    [
                        'attribute' => 'id_client',
                        'value' => function($model){
                            return Html::a($model->getClient(), Url::to(["clients/view", "id" => $model->id_client]));
                        },
                        'format' => "raw"
                    ],
                    // [
                    //     'attribute' => 'product',
                    //     'value' => function($model){
                    //         return $model->getProduct();
                    //     }
                    // ],
                    'amount',
                    [
                        'attribute' => 'color',
                        'value' => function($model){
                            return $model->getColor();
                        },
                    ],
                    'packaging',
                    'placeholder',
                    'notes:ntext',
                    [
                        'attribute' => 'total',
                        'value' => function($model){
                            return $model->formatNumber($model->total);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'deposit',
                        'value' => function($model){
                            return $model->formatNumber($model->deposit);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'balance',
                        'value' => function($model){
                            return $model->formatNumber($model->balance);
                        },
                        'format' => "raw"
                    ],
                    'shipping',
                    [
                        'attribute' => 'deadline',
                        'value' => function($model){
                            return $model->formatDate($model->deadline);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => "confirmed",
                        "value" => function($model){
                            return $model->confirmed ? "SI" : "NO";
                        }
                    ]
                ],
            ]) ?>

        </div>
    </div>  
    
    <div class="card card-info">
        <div class="card-header">
            <div class="text-md">Dettagli prodotti</div>
        </div>

        <div class="card-body">
       
            <?= GridView::widget([
                'dataProvider'  => $quoteDetails,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'id_product',
                        'value' => function($model){
                            return $model->getProduct();
                        },
                    ],
                    [
                        'attribute' => 'id_packaging',
                        'value' => function($model){
                            return $model->getPackaging();
                        },
                    ],
                    'amount',
                    [ 'class' => ActionColumn::className() ]
                ]
            ]); ?>
        </div>
        
    </div>


</div>
