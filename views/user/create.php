<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Occurrence */

$this->title = 'Aggiungi utente';
$this->params['breadcrumbs'][] = ['label' => 'Utenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="color-create">

    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
