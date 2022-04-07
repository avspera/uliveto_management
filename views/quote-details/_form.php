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

<div class="quote-details-form">

    <?php $form = ActiveForm::begin() ?>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'id_quote')->textInput(['maxlength' => true, "readonly" => true])->label("Numero ordine") ?></div>
        
        <div class="col-md-4 col-sm-6 col-12">
            <div class="form-group field-quote-details-id_product required">
                <label class="control-label" for="quote-details-id_packaging">Confezione</label>
                <select name="QuoteDetails['id_packaging']" class="form-control">
                    <?php foreach($packagings as $packaging) { ?>
                        <option selected=<?= $packaging->id == $model->id_packaging ? "selected" : "" ?>  value="<?= $packaging->id ?>"><?= $packaging->label." - ".$packaging->formatNumber($packaging->price) ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'id_color')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Color::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli'])->label('Colore'); ?> </div>
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'amount')->textInput(['maxlength' => true, 'type' => "number", 'min' => 0, 'step' => ".01", "placeholder" => "in ml"]) ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
