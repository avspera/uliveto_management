<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\Segnaposto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="segnaposto-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div class="row">
        <div class="col-md-4"><?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'price')->textInput(['maxlength' => true, "type" => "number", "step" => ".01"]) ?></div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <label>Immagine</label>
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
