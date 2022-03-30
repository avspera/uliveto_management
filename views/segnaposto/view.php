<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Segnaposto */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Segnaposto', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="segnaposto-view">
    <div class="card">
        <div class="card-header">
            <?= Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Cancella', ['delete', 'id' => $model->id], [
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
                    'label',
                    [
                        'attribute' => 'image',
                        'value' => function($model){
                            return !empty($model->image) ? 
                                 Html::img(Url::to(Yii::getAlias("@web")."/".$model->image), ['class' => 'img-fluid img-responsive', 'alt' => $model->label, 'title' => $model->label]) 
                             : "-";
                         },
                         'format' => "raw"
                     ],
                    [
                      'attribute' => 'price',
                      'value' => function($model){
                          return $model->formatNumber($model->price);
                      },
                      'format' => "raw"
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        }
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
