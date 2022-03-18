<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ClientSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="card card-success">
        <div class="card-header"> <i class="fas fa-search"></i> Cerca</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12"><?= $form->field($model, 'name') ?></div>
                <div class="col-md-3 col-sm-6 col-12"><?= $form->field($model, 'surname') ?></div>
                <div class="col-md-3 col-sm-6 col-12"><?= $form->field($model, 'email') ?></div>
                <div class="col-md-3 col-sm-6 col-12"><?= $form->field($model, 'phone') ?></div>
            </div>
            <div class="row">
                <div class="form-group">
                    <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
                    <?= Html::resetButton('Cancella filtri', ['class' => 'btn btn-outline-secondary']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
