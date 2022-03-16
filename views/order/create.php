<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Quote */
$this->title = 'Aggiungi Ordine';
$this->params['breadcrumbs'][] = ['label' => 'Ordini', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
</div>
