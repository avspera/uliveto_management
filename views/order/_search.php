<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\web\JsExpression;
use kartik\select2\Select2;
?>

<div class="quote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    
    <div class="card card-success">
        <div class="card-header"> <i class="fas fa-search"></i> Cerca</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12"><?= $form->field($model, 'order_number')->label("Numero ordine") ?></div>
                <div class="col-md-3 col-sm-6 col-12">
                    <label>Creato DA</label>
                    <?= DatePicker::widget([
                            'name' => 'OrderSearch[start_date]',
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <label>Creato A</label>
                    <?= DatePicker::widget([
                            'name' => 'OrderSearch[end_date]',
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <?= $form->field($model, 'id_client')->widget(Select2::classname(), [
                            'options' => [
                                'multiple'=>false, 
                                'placeholder' => 'Cerca cliente ...'
                            ],
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
                            'pluginEvents' => [
                                "select2:select" => "function(item) { getQuotes(item.params.data.id)}",
                            ]
                        ]);
                    ?>
                </div>
            </div>
            
            <div class="row">
                <div class="form-group">
                    <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
                    <?= Html::resetButton('Cancella filtri', ['class' => 'btn btn-outline-secondary']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php // echo $form->field($model, 'product') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'color') ?>

    <?php // echo $form->field($model, 'packaging') ?>

    <?php // echo $form->field($model, 'placeholder') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'total') ?>

    <?php // echo $form->field($model, 'deposit') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <?php // echo $form->field($model, 'shipping') ?>

    <?php // echo $form->field($model, 'deadline') ?>

    <?php ActiveForm::end(); ?>

</div>
