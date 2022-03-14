<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
$this->title = 'Modifica Cliente: '.$model->name." ".$model->surname;
$this->params['breadcrumbs'][] = ['label' => 'Clienti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-update">

    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
</div>
