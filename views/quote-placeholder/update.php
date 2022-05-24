<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\QuotePlaceholder */

$this->title = 'Modifica Preventivo Segnaposto '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Preventivi Segnaposto', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-placeholder-update">

    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', ['model' => $model, "placeholders" => $placeholders]) ?>
        </div>
    </div>

</div>
