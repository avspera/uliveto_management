<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Quote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'order_number')->textInput(["readonly" => true]) ?></div>
        <div class="col-md-4 col-sm-6 col-12">
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
                ]);
            ?>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'product')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Product::find()->orderBy('name')->all(), 'id', 'name'), ['prompt' => 'Scegli', "onChange" => "getProductInfo()"])->label('Prodotto'); ?>
        </div>
        
    </div>
   
    <div class="row">
        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'color')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Color::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli'])->label('Colore'); ?>
        </div>

        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'packaging')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Packaging::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli'])->label('Confezione'); ?>
        </div>
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'amount')->textInput(["onChange" => "updateTotalPrice(value)"]) ?></div>
        <!-- <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'placeholder')->checkbox() ?>
        </div> -->
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'deposit')->textInput(['maxlength' => true, "onchange" => "subtractDeposit()"]) ?></div>
        <div class="col-md-4 col-sm-4 col-12"><?php
            echo '<label class="form-label">Data </label>';
            echo DatePicker::widget([
                'value' => date("Y-m-d"),
                'name' => 'Quote[date_deposit]',
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]);
        ?></div>
    </div>
    <div class="row">
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'balance')->textInput(['maxlength' => true, "readonly" => true]) ?></div>
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'total')->textInput(['maxlength' => true, "readonly" => true]) ?></div>
        <div class="col-md-3 col-sm-4 col-12"><?php
            echo '<label class="form-label">Data saldo</label>';
            echo DatePicker::widget([
                'value' => date("Y-m-d"),
                'name' => 'Quote[date_balance]',
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]);
        ?></div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'shipping')->dropdownlist([0 => "NO", 1 => "SI"]) ?></div>
        <div class="col-md-3 col-sm-4 col-12"><?php
            echo '<label class="form-label">Consegna (entro il) </label>';
            echo DatePicker::widget([
                'value' => date("Y-m-d"),
                'name' => 'Quote[deadline]',
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]);
        ?></div>
    </div>
    

    <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    function getProductInfo(){
        let id_product = $("#quote-product").val();
        
        $.ajax({
            url: '<?= Url::to(['product/get-info']) ?>',
            type: 'get',
            dataType: 'json',
            'data': {
                'id': id_product,
            },
            success: function (data) {
                $('#quote-total').val(data.price)
            },
            error: function(error){
                console.log("error", error)
            }
        });
    }

    function updateTotalPrice(value){
        let currentTotal    = $('#quote-total').val();
        let updatedTotal    = currentTotal*value;
        let totalWithVat    = updatedTotal + (updatedTotal / 100) * 4;
        $('#quote-total').val(parseFloat(totalWithVat).toFixed(2))
    }

    function subtractDeposit(){
        let deposit         = $('#quote-deposit').val();
        let currentTotal    = $('#quote-total').val();
        let balance         = currentTotal-deposit;
        $('#quote-balance').val(parseFloat(balance).toFixed(2))
    }
</script>