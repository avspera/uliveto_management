<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Occurrence */

$this->title = 'Aggiungi occorrenza';
$this->params['breadcrumbs'][] = ['label' => 'Occorrenze', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="occurrence-create">

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
