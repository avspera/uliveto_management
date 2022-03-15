<?php

use yii\helpers\Html;

$this->title = 'Aggiungi pagamento';
$this->params['breadcrumbs'][] = ['label' => 'Pagamenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-create">
    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
</div>
