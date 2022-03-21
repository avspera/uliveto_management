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
        <div class="card-header"><?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?></div>
        <div class="card-body table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'email:email',
                    'name',
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
