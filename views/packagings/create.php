<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Occurrence */

$this->title = 'Aggiungi confezione';
$this->params['breadcrumbs'][] = ['label' => 'Confezioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="packaging-create">

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
