<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InviaCatalogoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invia Catalogo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invia-catalogo-index">

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

    <div class="card">
        <div class="card-header">
            <?= Html::a('<i class="fas fa-plus"></i> Aggiungi', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-file-pdf"></i> Carica cataloghi', ['upload-files'], ['class' => 'btn btn-info']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Cancella cataloghi', ['view-catalogs'], ['class' => 'btn btn-danger']) ?>

        </div>
        <div class="card-body table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'name',
                    'email:email',
                    'telefono',
                    [
                        'attribute' => "created_at",
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        },
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
                    [ 'class' => ActionColumn::className() ],
                ],
            ]); ?>
        </div>
    </div>

</div>
