<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\date\DatePicker;
use kartik\select2\Select2;

?>

<div class="payment-form">

    <?php $form = ActiveForm::begin(["enableClientValidation" => false]); ?>

    <div class="row">
        <div class="col-md-4 col-sm-12 col-12">
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
        <div class="col-md-4 col-sm-12 col-12">
            <?= $form->field($model, 'id_quote')->dropdownList([], ["onchange" => "getTotal()"])->label("BOMBONIERE") ?>
        </div>

        <div class="col-md-4 col-sm-12 col-12">
            <?= $form->field($model, 'id_quote_placeholder')->dropdownList([], ['prompt' => 'Scegli', "onchange" => "getTotal('placeholder')"])->label("SEGNAPOSTO") ?>
        </div>

    </div>
    
    <div class="row">

        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'total')->textInput(['prompt' => 'Scegli', "readonly" => true])->label("Totale") ?>
        </div>
        
        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'type')->dropdownList($model->types, ['prompt' => 'Scegli', "onchange" => "getAmount()"]) ?>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'amount')->textInput(["type" => "number", "step" => ".01"]) ?>
        </div>
        
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-12">
            <?php
                echo '<label class="form-label">Data</label>';
                echo DatePicker::widget([
                    'name' => 'Payment[created_at]',
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]);
            ?>
        </div>

        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'payed')->dropdownList([0 => "NO", 1 => "SI"], ['prompt' => 'Scegli']) ?>
        </div>
        <div class="col-md-4 col-sm-6 col-12">
            <?= $form->field($model, 'fatturato')->dropdownList([0 => "NO", 1 => "SI"], ['prompt' => 'Scegli']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    function getQuotes(id_client){
        console.log("id_client", id_client);
        $("#payment-id_quote").html("");
        $("#payment-id_quote").append("<option value=''></option>");
        $.ajax({
            url: '<?= Url::to(['quotes/get-by-client-id']) ?>',
            type: 'get',
            dataType: 'json',
            'data': {
                'id_client': id_client,
            },
            success: function (data) {
                if(data.status == "200")
                {
                    let htmlQuotes = "";
                    data.results.map((item, index) => {
                        htmlQuotes += "<option value="+item.id+">"+item.text+"</option>";
                    })
                    $("#payment-id_quote").append(htmlQuotes);

                    let htmlQuotesPlaceholder = "";
                    data.quotesPlaceholder.map((item, index) => {
                        htmlQuotesPlaceholder += "<option value="+item.id+">"+item.text+"</option>";
                    })
                    $("#payment-id_quote_placeholder").append(htmlQuotesPlaceholder);

                }else{
                    window.alert("Ops...something wrong here. [PAY-101]")
                }
            }
        });
    }

    function getAmount(){
        let type = $("#payment-type option:selected").val();   
        let id_quote = $("#payment-id_quote option:selected").val();
        
        if(!id_quote){
            id_quote = $("#payment-id_quote_placeholder option:selected").val();   
        }

        if(type){
            let url = '<?= Url::to(['payment/has-acconto']) ?>'
            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                'data': {
                    'id_quote': id_quote,
                },
                success: function (data) {
                    let total = $("#payment-total").val();
                    if(data.amount){
                        $("#payment-amount").val(parseFloat(total) - data.amount);
                    }else{
                        $("#payment-amount").val(parseFloat(total));
                    }
                }
            });
        }
        
    }

    function getTotal(flag = "quote"){
        let id_quote;
        let url = "";
        
        if(flag !== "quote"){
            id_quote = $("#payment-id_quote_placeholder option:selected").val();
            url = '<?= Url::to(['quote-placeholder/get-total']) ?>' 
        }else{
            id_quote = $("#payment-id_quote option:selected").val();
            url = '<?= Url::to(['quotes/get-total']) ?>' 
        }
        
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            'data': {
                'id_quote': id_quote,
            },
            success: function (data) {
                if(data.status == "200")
                {
                    $("#payment-total").val(data.total);
                }else{
                    window.alert("Ops...something wrong here. [PAY-102]")
                }
            }
        });
    }
</script>
