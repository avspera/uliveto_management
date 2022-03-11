<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Occurrence */

$this->title = 'Modifica occorrenza '.$model->label;
$this->params['breadcrumbs'][] = ['label' => 'Occorrenze', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="occurrence-create">

    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
