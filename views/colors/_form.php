<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Color */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="color-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'content')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'picture')->fileInput() ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
