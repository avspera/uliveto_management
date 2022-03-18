<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\date\DatePicker;

$this->title = 'Preventivi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="card">
        <div class="card-header"><?= Html::a('<i class="fas fa-plus"></i> Aggiungi', ['create'], ['class' => 'btn btn-success']) ?></div>
        <div class="card-body table table-responsive">
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
                            'language' => 'it',
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd',
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
