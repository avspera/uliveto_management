<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
        <!-- <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'color')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Color::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli'])->label('Color'); ?>
        </div> -->
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'id_packaging')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Packaging::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli'])->label('Confezione'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'price')->textInput(['maxlength' => true, 'type' => "decimal"]) ?></div>
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'capacity')->textInput(['maxlength' => true, 'type' => "decimal"]) ?></div>
        <div class="col-md-4 col-sm-6 col-12">
        <label>Immagine</label>
        <div class="input-group">
            <div class="custom-file">
                <input type="file" name="[Product][image]" class="custom-file-input" id="image">
                <label class="custom-file-label" for="exampleInputFile">Scegli</label>
            </div>
            <div class="input-group-append">
                <span class="input-group-text">Carica</span>
            </div>
        </div>
        </div>
    </div>

    
    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
