<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\QuotePlaceholder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-placeholder-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card card-success">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 col-sm-6 col-12">
                    <?= $form->field($model, 'id_quote')->widget(Select2::classname(), [
                            'options' => [
                                'multiple'=>false, 
                                'placeholder' => 'Cerca preventivo in base al cliente ...'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                ],
                                'ajax' => [
                                    'url' => Url::to(["segnaposto/search-from-select"]),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(client) { return client.text; }'),
                                'templateSelection' => new JsExpression('function (client) { return client.text; }'),
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-md-4 col-sm-6 col-12">
                    <?= $form->field($model, 'id_placeholder')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Segnaposto::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli']); ?>
                </div>
                <div class="col-md-4 col-sm-6 col-12">
                    <?= $form->field($model, 'amount')->textInput() ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
