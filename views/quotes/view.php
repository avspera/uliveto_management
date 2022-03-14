<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Quote */

$this->title = $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Preventivi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-view">

    <div class="card">
        <div class="card-header">
            <?= Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Cancella', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>

        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'order_number',
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
                        'attribute' => 'id_client',
                        'value' => function($model){
                            return $model->getClient();
                        }
                    ],
                    [
                        'attribute' => 'product',
                        'value' => function($model){
                            return $model->getProduct();
                        }
                    ],
                    'amount',
                    'color',
                    'packaging',
                    'placeholder',
                    'notes:ntext',
                    [
                        'attribute' => 'total',
                        'value' => function($model){
                            return $model->formatNumber($model->total);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'deposit',
                        'value' => function($model){
                            return $model->formatNumber($model->deposit);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'balance',
                        'value' => function($model){
                            return $model->formatNumber($model->balance);
                        },
                        'format' => "raw"
                    ],
                    'shipping',
                    [
                        'attribute' => 'deadline',
                        'value' => function($model){
                            return $model->formatDate($model->deadline);
                        },
                        'format' => "raw"
                    ],
                ],
            ]) ?>

        </div>
    </div>    
</div>
