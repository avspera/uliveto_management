<?php

use yii\helpers\Html;
use yii\helpers\Url;
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
            <?= Html::a('<i class="fas fa-pencil-alt"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php 
                if(Yii::$app->user->identity->isAdmin()) 
                    echo Html::a('<i class="fas fa-trash"></i> Cancella', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Sei sicuro di voler cancellare questo elemento?',
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
                    [
                       'attribute' => 'image',
                       'value' => function($model){
                           return !empty($model->image) ? 
                                Html::img(Url::to(Yii::getAlias("@web")."/".$model->image), ['class' => 'img-fluid img-responsive', 'alt' => $model->name, 'title' => $model->name]) 
                            : "-";
                        },
                        'format' => "raw"
                    ],
                    'weight',
                    [
                        'attribute' => 'id_packaging',
                        'value' => function($model){
                            return $model->getPackaging();
                        },
                    ],
                    [
                      'attribute' => 'price',
                      'value' => function($model){
                          return $model->formatNumber($model->price);
                      },
                      'format' => 'raw'
                    ],
                    [
                        'attribute' => 'capacity',
                        'value' => function($model){
                            return $model->capacity." ml";
                        }
                    ]
                ],
            ]) ?>
        </div>
    </div>

</div>
