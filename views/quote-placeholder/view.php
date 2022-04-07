<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\QuotePlaceholder */

$this->title = $model->id." - ".$model->getQuoteInfo();
$this->params['breadcrumbs'][] = ['label' => 'Preventivi Segnaposto', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-placeholder-view">

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
                        'attribute' => 'id_quote',
                        'value' => function($model){
                            return Html::a($model->getQuoteInfo(), Url::to(["quotes/view", "id" => $model->id_quote]));
                         },
                         'format' => "raw"
                    ],
                    [
                        'attribute' => 'id_placeholder',
                        'value' => function($model){
                            return $model->getPlaceholderInfo();
                        },
                        'format' => "raw"
                     ],
                    'amount',
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
                ],
            ]) ?>
        </div>
    </div>
</div>
