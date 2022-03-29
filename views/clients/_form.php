<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">

    <?php $form = ActiveForm::begin(); ?>
  
        <div class="row">
            <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'email')->textInput(['maxlength' => true, "type" => "email"]) ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'age')->textInput(['maxlength' => true, 'type' => 'number']) ?></div>
            <div class="col-md-4 col-sm-6 col-12">
                <?= $form->field($model, 'occurrence')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Occurrence::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli'])->label('Occorrenza'); ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
        </div>


    <?php ActiveForm::end(); ?>

</div>
