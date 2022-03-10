<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
$this->title = 'Aggiungi Cliente';
$this->params['breadcrumbs'][] = ['label' => 'Clienti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-create">

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
</div>
