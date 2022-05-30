<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\jui\DatePicker;
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
            <div class="form-group field-payment-id_quote">
                <label class="control-label" for="payment-id_quote">BOMBONIERE</label>
                <select onchange="getTotal()" id="payment-id_quote" class="form-control" name="Payment[id_quote]" onchange="getTotal()">
                    <?php if(!empty($model->id_quote)) ?>
                        <option value=<?= $model->id_quote ?>><?= $model->id_quote ?></option>
                    <?php ?>
                </select>

                <div class="help-block"></div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 col-12">
            <div class="form-group field-payment-id_quote">
                <label class="control-label" for="payment-id_quote_placeholder">SEGNAPOSTO</label>
                <select id="payment-id_quote_placeholder" class="form-control" name="Payment[id_quote_placeholder]" onchange="getTotal('placeholder')">
                    <?php if(!empty($model->id_quote_placeholder)) ?>
                        <option value=<?= $model->id_quote_placeholder ?>><?= $model->id_quote_placeholder ?></option>
                    <?php ?>
                </select>

                <div class="help-block"></div>
            </div>
        </div>

    </div>
    
    <div class="row">

        <div class="col-md-4 col-sm-6 col-12">
            <div class="form-group field-payment-total">
                <label class="control-label" for="payment-total">Totale</label>
                <input value="<?= $model->getTotal() ?>" type="text" id="payment-total" class="form-control" name="Payment[total]" readonly="" prompt="Scegli">
                <div class="help-block"></div>
            </div>
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
            <?= $form->field($model, 'created_at')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'it',
                'dateFormat' => 'yyyy-MM-dd',
                'options' => [
                    'class' => "form-control",
                    'autocomplete' => false
                ],
                'clientOptions' => [
                    'minDate' => "today",
                    'changeMonth' => true, 
                    'changeYear' => true,
                ]
            ]) ?>
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
        
        $("#payment-id_quote").html("");
        $("#payment-id_quote").append("<option value=''></option>");
        $("#payment-id_quote_placeholder").html("");
        $("#payment-id_quote_placeholder").append("<option value=''></option>");
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
                    console.log("data.amount", data.amount);
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
        console.log("url", url);
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
