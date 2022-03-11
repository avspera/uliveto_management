<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Prodotti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view">

    <div class="card">
        <div class="card-header">
            <?= Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php 
                if(Yii::$app->user->identity->isAdmin()) 
                    echo Html::a('Cancella', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) 
            ?>
        </div>

        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'image',
                    'weight',
                    'id_packaging',
                    [
                      'attribute' => 'price',
                      'value' => function($model){
                          return $model->formatNumber($model->price);
                      },
                      'format' => 'raw'
                    ],
                    'capacity',
                ],
            ]) ?>
        </div>
    </div>

</div>
