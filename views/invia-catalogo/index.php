<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InviaCatalogoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invia Catalogo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invia-catalogo-index">

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
                            'name' => 'created_at',
                            'language' => 'it',
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd',
                            ]
                        ])
                    ],
                    [ 'class' => ActionColumn::className() ],
                ],
            ]); ?>
        </div>
    </div>

</div>
