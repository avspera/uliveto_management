<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PackagingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Confezioni';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="packaging-index">

    <div class="card">
        <div class="card-body table-responsive">

            <p><?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?></p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'name',
                    'label',
                    [
                       'attribute' => 'price',
                       'value' => function($model){
                           return $model->formatNumber($model->price);
                       }
                    ],
                    'image',
                    [
                        'class' => ActionColumn::className()
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
