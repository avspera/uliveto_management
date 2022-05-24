<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use kartik\date\DatePicker;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = $model->name." ".$model->surname;
$this->params['breadcrumbs'][] = ['label' => 'Clienti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="client-view">

    <?php if(Yii::$app->session->hasFlash('error')): ?>
      <div class="alert alert-warning alert-dismissible" style="color: white">
        <?php echo Yii::$app->session->getFlash('error'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?> 

    <?php if(Yii::$app->session->hasFlash('success')): ?>
      <div class="alert alert-success alert-dismissible">
        <?php echo Yii::$app->session->getFlash('success'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?> 

    <div class="row">
        <div class="col-md-6 col-sm-6 col-12">
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
                </div>

                <div class="card-body table-responsive">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'name',
                            'surname',
                            'email:email',
                            'phone',
                            'age',
                            [
                                'attribute' => 'occurrence',
                                'value' => function($model){
                                    return $model->getOccurrence();
                                }   
                            ]
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-12">
            <div class="card card-info">
                <div class="card-header">
                    <div class="text-md">Preventivi / ordini associati 
                        <a href="<?= Url::to(["quotes/create", "id_client" => $model->id]) ?>">
                            <i style="cursor: pointer; margin-left: 5px" class="fas fa-plus"></i>
                        </a>
                    </div>
                    
                </div>
                <div class="card-body">
                    <?php if(!empty($quotes)) { ?>
                        <?= GridView::widget([
                            'dataProvider'=> $quotes,
                            'columns' => [
                                'order_number',
                                [
                                    'attribute' => 'id_client',
                                    'value' => function($model){
                                        return Html::a($model->getClient(), Url::to(["clients/view", "id" => $model->id_client]));
                                    },
                                    'format' => "raw"
                                ],
                                // [
                                //    'attribute' => 'product',
                                //    'value' => function($model){
                                //        return $model->getProduct();
                                //    },
                                //    'filter' => yii\helpers\ArrayHelper::map(app\models\Product::find()->orderBy('name')->all(), 'id', 'name')
                                // ],
                                [
                                    'attribute' => 'created_at',
                                    'value' => function($model){
                                        return $model->formatDate($model->created_at);
                                    },
                                    'format' => "raw",
                                    'filter' => DatePicker::widget([
                                        'name' => 'created_at',
                                        'language' => "it",
                                        'type' => DatePicker::TYPE_INPUT,
                                        'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'yyyy-mm-dd',
                                            'startDate' => date("Y-m-d")
                                        ]
                                    ])
                                ],
                                [
                                    'attribute' => 'deadline',
                                    'value' => function($model){
                                        return $model->formatDate($model->deadline);
                                    },
                                    'format' => "raw"
                                ],
                                [
                                    'attribute' => 'updated_at',
                                    'value' => function($model){
                                        return $model->formatDate($model->updated_at);
                                    },
                                    'format' => "raw"
                                ],
                                // 'amount',
                                [
                                'attribute' => 'confirmed',
                                'value' => function($model){
                                    return $model->confirmed ? "SI" : "NO";
                                },
                                'filter' => [0 => "NO", 1 => "SI"] 
                                ],
                                //'total',
                                //'deposit',
                                //'balance',
                                //'shipping',
                                //'deadline',
                                [
                                    'class' => ActionColumn::className(),
                                ],
                            ],
                            'toolbar' => [
                                [
                                    'content'=>
                                        Html::button('<i class="fas fa-plus"></i>', [
                                            'type'=>'button', 
                                            'title'=>'Add Book',
                                            'class'=>'btn btn-success'
                                        ]) . ' '.
                                        Html::a('<i class="fas fa-redo"></i>', ['grid-demo'], [
                                            'class' => 'btn btn-secondary btn-default', 
                                            'title' => 'Reset Grid'
                                        ]),
                                ],
                            ]
                        ]); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    
</div>
