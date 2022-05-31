<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use yii\jui\DatePicker;

$this->title = 'Preventivi Bomboniere';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-index">
    
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

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="card">
        <div class="card-header">
            <?= Html::a('<i class="fas fa-plus"></i> Aggiungi', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Cancella selezionati', ['delete-all'], ['class' => 'btn btn-danger']) ?>
        </div>
        <div class="card-body table table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'responsive'=>true,
                'hover'=>true,
                'columns' => [
                    //multiselect
                    // [
                    //     'class' => 'yii\grid\CheckboxColumn', 
                    //     'checkboxOption' => ["value" => $model->id],
                    // ],
                    'order_number',
                    [
                        'attribute' => 'id_client',
                        'value' => function($model){
                            return Html::a($model->getClient(), Url::to(["clients/view", "id" => $model->id_client]));
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'product',
                        'value' => function($model){
                            return $model->getProducts();
                        },
                        'filter' => yii\helpers\ArrayHelper::map(app\models\Product::find()->orderBy('name')->all(), 'id', 'name')
                     ],
                    [
                        'attribute' => "has_segnaposto",
                        'value' => function($model){
                            $segnaposto = $model->getSegnaposto();
                            return !empty($segnaposto) ? Html::a("Vai al preventivo", Url::to(["/quote-placeholder/view", "id" => $segnaposto->id])) : "-" ;
                        },
                        'format' => "raw",
                        'label' => "Segnaposto"
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        },
                        'format' => "raw",
                        'filter' => DatePicker::widget([
                            'name'  => 'created_at',
                            'language' => 'it',
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => [
                                'class' => "form-control",
                                'autocomplete' => false
                            ],
                            'clientOptions' => [
                                'changeMonth' => true, 
                                'changeYear' => true,
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
                    
                    [
                        'class' => ActionColumn::className(),
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
