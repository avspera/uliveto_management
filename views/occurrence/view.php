<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Occurrence */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Occorrenze', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="occurrence-view">
    <div class="card">
        <div class="card-header">
            <p>
                <?= Html::a('<i class="fas fa-pencil-alt"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="fas fa-trash"></i> Cancella', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        </div>

        <dic class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'label',
                ],
            ]) ?>
        </dic>
    </div>


</div>
