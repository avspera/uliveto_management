<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Packaging */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Confezioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="packaging-view">

        <div class="card">
            <div class="card-header">
                <?= Html::a('<i class="fas fa-pencil-alt"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="fas fa-trash"></i> Cancella', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Sei sicuro di voler cancellare questo elemento?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>

            <div class="card-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        [
                            'attribute' => 'id_product',
                            'value' => function($model){
                                return $model->getProduct();
                            },
                            'filter' => yii\helpers\ArrayHelper::map(app\models\Product::find()->orderBy('name')->all(), 'id', 'name')
                        ],
                        'name',
                        'label',
                        [
                            'attribute' => "price",
                            'value' => function($model){
                              return $model->formatNumber($model->price);
                            },
                            'format' => "raw"
                        ],
                        [
                            'attribute' => 'image',
                            'value' => function($model){
                                return !empty($model->image) ? 
                                     Html::img(Url::to(Yii::getAlias("@web")."/".$model->image), ['class' => 'img-fluid img-responsive', 'alt' => $model->name, 'title' => $model->name]) 
                                 : "-";
                             },
                             'format' => "raw"
                         ],
                    ],
                ]) ?>
            </div>
        </div>

</div>
