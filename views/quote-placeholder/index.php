<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\QuotePlaceholderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Preventivi segnaposto';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-placeholder-index">

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
            <?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <div class="card-body table-responsive">
            <?= GridView::widget([
                'dataProvider'  => $dataProvider,
                'filterModel'   => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                       'attribute' => 'id_quote',
                       'value' => function($model){
                           return Html::a($model->getQuoteInfo(), Url::to(["quote/view", "id" => $model->id]));
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'id_placeholder',
                        'value' => function($model){
                            return $model->getPlaceholderInfo();
                        },
                        'format' => "raw",
                        'filter' => yii\helpers\ArrayHelper::map(app\models\Segnaposto::find()->orderBy('label')->all(), 'id', 'label')
                    ],
                    'amount',
                    // [
                    //     'attribute' => "total_no_vat",
                    //     'value' => function($model){
                    //         return $model->getTotal();
                    //     },
                    //     'format' => "raw",
                    //     'label' => "Totale senza iva"
                    // ],
                    [
                        'attribute' => "total",
                        'value' => function($model){
                            return $model->getTotal("vat");
                        },
                        'format' => "raw",
                        'label' => "Totale"
                    ],
                    [
                        'attribute' => 'date_deposit',
                        'value' => function($model){
                            return $model->formatDate($model->date_deposit);
                        },
                        'format' => "raw",
                        'filter' => DatePicker::widget([
                            'name' => 'date_deposit',
                            'language' => 'it',
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd',
                                'startDate' => date("Y-m-d")
                            ]
                        ])
                    ],
                    [
                        'attribute' => 'acconto',
                        'value' => function($model){
                            return $model->formatNumber($model->acconto);
                        },
                    ],
                    [
                        'attribute' => 'date_balance',
                        'value' => function($model){
                            return $model->formatDate($model->date_balance);
                        },
                        'format' => "raw",
                        'filter' => DatePicker::widget([
                            'name' => 'date_balance',
                            'language' => 'it',
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd',
                                'startDate' => date("Y-m-d")
                            ]
                        ])
                    ],
                    [
                        'attribute' => 'saldo',
                        'value' => function($model){
                            return $model->formatNumber($model->acconto);
                        },
                    ],
                    [
                        'attribute' => "confirmed",
                        'value' => function($model){
                            return $model->confirmed ? "SI" : "NO";
                        },
                        'filter' => [0 => "NO", 1 => "SI"]
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function($model){
                            return $model->formatDate($model->updated_at);
                        }
                    ],
                    [
                        'class' => ActionColumn::className(),
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
