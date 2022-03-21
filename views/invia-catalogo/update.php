<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Occurrence */

$this->title = 'Modifica invio catalogo';
$this->params['breadcrumbs'][] = ['label' => 'Invio cataloghi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invia-catalogo-update">

    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
