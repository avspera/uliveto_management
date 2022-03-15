<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;

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
                           return $model->getProduct();
                       }
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
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
                    'amount',
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
