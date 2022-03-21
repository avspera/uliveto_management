<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\InviaCatalogo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invia-catalogo-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => "email"]) ?></div>
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Invia', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
