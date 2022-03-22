<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sconti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-index">

    <div class="card">
        <div class="card-header"><?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?></div>
        <div class="card-body table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'name',
                    [
                        'attribute' => 'amount',
                        'value' => function($model){
                            return $model->amount." %";
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        }
                    ],
                    ['class' => ActionColumn::className()],
                ],
            ]); ?>        
        </div>
    </div>

</div>
