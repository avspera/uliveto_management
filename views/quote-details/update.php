<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Payment */

$this->title = 'Modifica dettagli Ordine: ' . $model->getQuoteInfo();
$this->params['breadcrumbs'][] = ['label' => 'Pagamenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="payment-update">
    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', [
                'model'         => $model, 
                "products"      => $products,
                "packagings"    => $packagings
            ]) ?>
        </div>
    </div>
</div>
