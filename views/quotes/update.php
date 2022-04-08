<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Quote */

$this->title = 'Modifica Preventivo: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Preventivi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="quote-update">
    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', [
                'model'             => $model, 
                'segnaposto'        => $segnaposto,
                'quoteDetails'      => $quoteDetails,
                'products'          => $products,
                'detailsModel'      => $detailsModel,
                'colors'            => $colors,
                'packagings'        => $packagings,
                'currentBottleAmount' => $currentBottleAmount
            ]) ?>
        </div>
    </div>
</div>

