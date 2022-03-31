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

$text = json_encode("ciao come stai?")
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
            <?= Html::a('<i class="fas fa-file-pdf"></i> Genera PDF', ['generate-pdf', 'id' => $model->id, 'flag' => "generate"], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-comment"></i> Whatsapp', Url::to("https://wa.me/00393339128349/?text=".$text), ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-check"></i> Conferma', ['confirm', 'id' => $model->id, 'flag' => "send"], ['class' => 'btn btn-info']) ?>
            

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
                    'packaging',
                    [
                        'attribute' => 'placeholder',
                        'value' => function($model){
                            return $model->getPlaceholder()." - ".$model->getPlaceholderTotal();
                        }
                    ],
                    [
                        'attribute' => "custom_amount",
                        'value' => function($model){
                            return !empty($model->custom_amount) ? $model->formatNumber($model->custom_amount) : "-";
                        }
                    ],
                    'custom:ntext',
                    'notes:ntext',
                    [
                        'attribute' => 'total_no_vat',
                        'value' => function($model){
                            return $model->formatNumber($model->total_no_vat);
                        },
                        'format' => "raw"
                    ],
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
                    [
                        'attribute' => 'shipping',
                        'value' => function($model){
                            return $model->shipping ? "SI" : "NO";
                        }
                    ],
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
    
    <div class="row">
        <div class="col-md-6">
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
            </div>
        <?php } ?> 
    </div>

</div>