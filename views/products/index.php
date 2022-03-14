<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Prodotti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <div class="card">
        <div class="card-body table-responsive">

            <p><?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?></p>

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'name',
                    'image',
                    'weight',
                    [//'id_packaging',]
                    [
                        'attribute' => 'price',
                        'value' => function($model){
                            return $model->formatNumber($model->price);
                        },
                        'format' => "raw"
                    ],
                    'capacity',
                    [
                        'class' => ActionColumn::className(),
                    ],
                ],
            ]]); ?>
        </div>
    </div>

</div>
