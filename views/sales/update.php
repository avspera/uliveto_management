<?php

use yii\helpers\Html;

$this->title = 'Modifica Sconto: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Sconti', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';

?>
<div class="sales-create">

    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
</div>

