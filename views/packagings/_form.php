<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\Packaging */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="packaging-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-sm-6 col-12"><?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-sm-6 col-12"><?= $form->field($model, 'id_product')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Product::find()->orderBy('name')->all(), 'id', 'name'),['maxlength' => true]) ?></div>
        <div class="col-md-3 col-sm-6 col-12"><?= $form->field($model, 'price')->textInput(['maxlength' => true, 'type' => "number", 'min' => 0, 'step' => ".01"]) ?></div>    
    </div>
    
    <div class="row">
        <div class="col-12">
            <?= FileInput::widget([
                'model' => $model,
                'attribute' => 'image',
                'options' => ['multiple' => false, 'accept' => ["png", "jpg"]]
            ]);?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
