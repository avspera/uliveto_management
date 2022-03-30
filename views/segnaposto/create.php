<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */
$this->title = 'Aggiungi Segnaposto';
$this->params['breadcrumbs'][] = ['label' => 'Segnaposto', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="segnaposto-create">

    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
</div>
