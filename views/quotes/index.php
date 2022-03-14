<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
// use yii\grid\GridView;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\QuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Preventivi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-index">

    <p><?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="card">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'responsive'=>true,
                'hover'=>true,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'order_number',
                    'id_client',
                    [
                       'attribute' => 'product',
                       'value' => function($model){
                           return $model->getProduct();
                       }
                    ],
                    'created_at',
                    'updated_at',
                    //'amount',
                    //'color',
                    //'packaging',
                    //'placeholder',
                    //'notes:ntext',
                    //'total',
                    //'deposit',
                    //'balance',
                    //'shipping',
                    //'deadline',
                    [
                        'class' => ActionColumn::className(),
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
