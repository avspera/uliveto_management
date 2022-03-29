<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4"><?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'email')->textInput(['type' => 'email']) ?></div>
    </div>

    <div class="row">
        <div class="col-md-4"><?= $form->field($model, 'password')->textInput(['maxlength' => true, 'type' => "password"]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'status')->dropdownlist($model->statusList, ['prompt' => "Scegli"]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'role')->dropdownlist($model->roleList, ['prompt' => "Scegli"]) ?></div>
    </div>
    
    <?php if(Yii::$app->controller->action->id == "update"){ ?>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nova password</label>
                    <input class="form-control" name="User[new_password]" type="password">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Conferma nuova password</label>
                    <input class="form-control" name="User[new_password_confirm]" type="password">
                </div>
            </div>
        </div>
    <?php } ?>
    
    <!-- <div class="row">
        <div class="col-12">
            <label>Foto del profilo</label>
            <?php
                //  FileInput::widget([
                //     'model' => $model,
                //     'attribute' => 'picture',
                //     'options' => ['multiple' => false, 'accept' => ["png", "jpg"]]
                // ]);
            ?>
        </div>
    </div> -->
    
    
    <div class="form-group" style="margin-top: 10px">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
