<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Quote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'order_number')->textInput() ?></div>
        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'id_client')->widget(Select2::classname(), [
                    'options' => ['multiple'=>true, 'placeholder' => 'Cerca cliente ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(["clients/search-from-select"]),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(client) { return client.text; }'),
                        'templateSelection' => new JsExpression('function (client) { return client.text; }'),
                    ],
                ]);
            ?>
        </div>
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'product')->textInput() ?></div>
    </div>
   
    <div class="row">
        <div class="col-md-4 col-sm-6 col-12">
            <label>Colore</label>
            <img src="<?= Yii::getAlias("@web") ?>/images/colors/light_blue.png" />
            <img src="<?= Yii::getAlias("@web") ?>/images/colors/light_blue.png" />
            <img src="<?= Yii::getAlias("@web") ?>/images/colors/light_blue.png" />
            <img src="<?= Yii::getAlias("@web") ?>/images/colors/light_blue.png" />
            <img src="<?= Yii::getAlias("@web") ?>/images/colors/light_blue.png" />
            <img src="<?= Yii::getAlias("@web") ?>/images/colors/light_blue.png" />
            <img src="<?= Yii::getAlias("@web") ?>/images/colors/light_blue.png" />
            <img src="<?= Yii::getAlias("@web") ?>/images/colors/light_blue.png" />
        </div>

        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'packaging')->textInput() ?>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'placeholder')->checkbox() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'amount')->textInput() ?></div>
        <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'deposit')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'balance')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'total')->textInput(['maxlength' => true]) ?></div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'shipping')->textInput() ?></div>
        <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'deadline')->textInput() ?></div>
    </div>
    

    <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
