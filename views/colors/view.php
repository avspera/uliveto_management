<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Color */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Colori', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="color-view">

    <div class="card">
        <div class="card-header">
            <?= Html::a('<i class="fas fa-pencil-alt"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Cancella', ['delete', 'id' => $model->id], [
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
                    'label',
                    'content',
                    'picture',
                ],
            ]) ?>
        </div>
    </div>

</div>
