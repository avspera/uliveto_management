<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $model app\models\Quote */
    $this->title = $model->order_number." - ".$client;
    $this->params['breadcrumbs'][] = ['label' => 'Ordini', 'url' => ['index']];
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
            <?= Html::a('<i class="fas fa-file-pdf"></i> Genera PDF', ['/quotes/generate-pdf', 'id' => $model->id, 'flag' => "generate"], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-envelope"></i> Invia PDF', ['/quotes/generate-pdf', 'id' => $model->id, 'flag' => "send"], ['class' => 'btn btn-success']) ?>
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
                    [
                        'attribute' => 'placeholder',
                        'value' => function($model){
                            return $model->getPlaceholder()." - ".$model->getPlaceholderTotal();
                        }
                    ],
                    [
                        'attribute' => "custom_amount",
                        'value' => function($model){
                            return $model->formatNumber($model->custom_amount);
                        }
                    ],
                    'custom:ntext',
                    'notes:ntext',
                    [
                        'attribute' => 'total',
                        'value' => function($model){
                            return $model->formatNumber($model->total);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'id_sconto',
                        'value' => function($model){
                            return $model->getSale($model->id_sconto);
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
                'dataProvider'  => $products,
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

    <?php if(!empty($segnaposto)) { ?>
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <div class="text-md">Servizi aggiuntivi</div>
                </div>

                <div class="card-body">
            
                    <?= DetailView::widget([
                        'model' => $segnaposto,
                        'attributes' => [
                            'id',
                            'label',
                            [
                                'attribute' => 'image',
                                'value' => function($model){
                                    return !empty($model->image) ? 
                                        Html::img(Url::to(Yii::getAlias("@web")."/".$model->image), ['class' => 'img-fluid img-responsive', 'alt' => $model->label, 'title' => $model->label]) 
                                    : "-";
                                },
                                'format' => "raw"
                            ],
                            [
                            'attribute' => 'price',
                            'value' => function($model){
                                return $model->formatNumber($model->price);
                            },
                            'format' => "raw"
                            ],
                            [
                                'attribute' => 'created_at',
                                'value' => function($model){
                                    return $model->formatDate($model->created_at);
                                }
                            ],
                        ],
                    ]) ?>
                </div>
                    
            </div>
        </div>
    <?php } ?> 

    <div class="card card-success">
        <div class="card-header">
            <div class="text-md">Pagamenti</div>
        </div>

        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $payments,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
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
                        'attribute' => 'amount',
                        'value' => function($model){
                            return $model->formatNumber($model->amount);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        }
                    ],
                ]
            ]); ?>
        </div>

    </div>
</div>
