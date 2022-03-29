<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Quote */
$this->title = 'Aggiungi Preventivo';
$this->params['breadcrumbs'][] = ['label' => 'Preventivi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-create">
    <?= $this->render('_form', [
        'model'     => $model,
        "products"  => $products, 
        'packagings' => $packagings,
    ]) ?>
</div>
