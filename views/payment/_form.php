<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\date\DatePicker;
use kartik\select2\Select2;

?>

<div class="payment-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6 col-sm-6 col-12">
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
        <div class="col-md-6 col-sm-6 col-12">
            <?= $form->field($model, 'id_quote')->dropdownList([], ['prompt' => 'Scegli']) ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 col-sm-6 col-12">
            <?= $form->field($model, 'amount')->textInput(["type" => "number"]) ?>
        </div>
        
        <div class="col-md-6 col-sm-6 col-12">
            <?php
                echo '<label class="form-label">Data</label>';
                echo DatePicker::widget([
                    'value' => date("Y-m-d"),
                    'name' => 'Payment[created_at]',
                    'type' => DatePicker::TYPE_INPUT,
                    'language' => 'it',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]);
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    function getQuotes(id_client){
        $("#payment-id_quote").html("");
        $("#payment-id_quote").append("<option>Scegli</option>");
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
                    let html = "";
                    data.results.map((item, index) => {
                        html += "<option value="+item.id+">"+item.text+"</option>";
                    })
                    // $.each( data.results, function (i, val) {
                    //     html += "<option value="+i+">"+val+"</option>";
                    // } );
                    $("#payment-id_quote").append(html);
                }else{
                    window.alert("Ops...something wrong here. [PAY-101]")
                }
            }
        });
    }
</script>
