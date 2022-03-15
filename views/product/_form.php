<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use app\utils\FKUploadUtils; 
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'id_packaging')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Packaging::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli'])->label('Confezione'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'price')->textInput(['maxlength' => true, 'type' => "decimal"]) ?></div>
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'capacity')->textInput(['maxlength' => true, 'type' => "decimal", "placeholder" => "in ml"]) ?></div>
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
