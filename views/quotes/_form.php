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

    <div class="card card-success">
    <div class="card-body table-responsive">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-12"><?= $form->field($model, 'order_number')->textInput(["readonly" => true]) ?></div>
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
                        ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-info">
        <div class="card-header">
            <div class="row">
                <div class="text-lg">Prodotti</div>    
                <div class="text-md" style="cursor:pointer" onclick="addProductLine()"><i style="margin-top:7px; margin-left:7px" class="fas fa-plus-circle" ></i></div>
            </div>
            
        </div>
        <div class="card-body table-responsive">
            <div class="row prod" id="prod_0">
                <div class="col-md-3 col-sm-6 col-12">
                    <?= $form->field($model, 'product[0]')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Product::find()->orderBy('name')->all(), 'id', 'name'), ['prompt' => 'Scegli', "onChange" => "getProductInfo(0)"])->label('Prodotto'); ?>
                </div>
                <div class="col-md-1 col-sm-4 col-12"><?= $form->field($model, 'amount[0]')->textInput(["onChange" => "updateTotalPrice(value)"]) ?></div>
                <div class="col-md-2 col-sm-6 col-12">
                    <?= $form->field($model, 'color[0]')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Color::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli'])->label('Colore'); ?>
                </div>
                <div class="col-md-2 col-sm-4 col-12"><?= $form->field($model, 'custom_color[0]')->textInput(["maxlenght" => true]) ?></div>
                <div class="col-md-3 col-sm-6 col-12">
                    <?= $form->field($model, 'packaging')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Packaging::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli'])->label('Confezione'); ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card card-info">
        <div class="card-header">
            <div class="text-lg text-white">Servizi aggiuntivi</div>    
        </div>
        <div class="card-body table-responsive">

            <div class="row">
                <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'confetti')->dropdownlist([0 => "NO", 1 => "SI"], ['prompt' => "Scegli"]) ?></div>
                <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'prezzo_confetti')->textInput(['maxlength' => true, "onchange" => "addPrezzoConfetti(value)"]); ?></div>
                <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'confetti_omaggio')->checkBox(["onChange" => "removePrezzoConfetti()"]); ?></div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'custom_amount')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-9 col-sm-12 col-12"><?= $form->field($model, 'custom')->textarea(['rows' => 6]) ?></div>    
            </div>
        </div>
    </div>

    <div class="card card-secondary">
        <div class="card-header">
            <div class="text-lg">Altro</div>    
        </div>
        <div class="card-body table-responsive">
            <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="card card-secondary">
        <div class="card-header">
            <div class="text-lg">Costi</div>    
        </div>
        <div class="card-body table-responsive">

            <div class="row">
                <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'id_sconto')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Sales::find()->orderBy('name')->all(), 'id', 'name'), ['prompt' => 'Scegli', "onChange" => "applySales()"]); ?></div>
                <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'total_no_vat')->textInput(['maxlength' => true, "readonly" => true]) ?></div>
                <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'total')->textInput(['maxlength' => true, "readonly" => true]) ?></div>
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
                <div class="col-md-4 col-sm-4 col-12">
                    <?php
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
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'shipping')->dropdownlist([0 => "NO", 1 => "SI"]) ?></div>
                <div class="col-md-4 col-sm-6 col-12"><?= $form->field($model, 'address')->textInput(["maxlength" => true]) ?></div>
                <div class="col-md-4 col-sm-4 col-12"><?php
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

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>
    
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    function getProductInfo(index){
        let id_product = $(`#quote-product-${index}`).val();
        console.log("id_prodiuct", id_product)
        $.ajax({
            url: '<?= Url::to(['product/get-info']) ?>',
            type: 'get',
            dataType: 'json',
            'data': {
                'id': id_product,
            },
            success: function (data) {
                let currentTotal = $('#quote-total').val();
                $('#quote-total').val(parseFloat(currentTotal + data.price).toFixed(2))
                console.log("currentTotal", parseFloat(currentTotal + data.price).toFixed(2))
                $('#quote-total_no_vat').val(parseFloat(currentTotal + data.price).toFixed(2))
            },
            error: function(error){
                console.log("error", error)
            }
        });
    }

    function updateTotalPrice(value){
        let currentTotal    = $('#quote-total').val();
        let updatedTotal    = currentTotal*value;
        console.log("updatedTotal", updatedTotal)
        let totalWithVat    = updatedTotal + (updatedTotal / 100) * 4;
        console.log("total_with_cat", totalWithVat);
        $('#quote-total').val(parseFloat(totalWithVat).toFixed(2))
        $('#quote-total_no_vat').val(parseFloat(updatedTotal).toFixed(2))
    }

    function subtractDeposit(){
        let deposit         = $('#quote-deposit').val();
        let currentTotal    = $('#quote-total').val();
        let balance         = currentTotal-deposit;
        $('#quote-balance').val(parseFloat(balance).toFixed(2))
    }

    function removeProductLine(index){
        console.log("index", index);
        let id = `#prod_${index}`;
        $(id).remove()
    }

    function addPrezzoConfetti (price) {
        let currentTotal    = $('#quote-total').val();
        $('#quote-total').val(parseFloat(currentTotal+price).toFixed(2))
    }

    function removePrezzoConfetti () {
        let priceConfetti    = $('#quote-prezzo_confetti').val();
        $('#quote-total').val(parseFloat(currentTotal-priceConfetti).toFixed(2))
    }

    function addProductLine(){
        let index   = $("div[id^='prod_']").attr("id");
        let node    = $("div[id^='prod_']");
        index       = index.substr(index.indexOf("_")+1, 1);
        index       = parseInt(index+1)
        let html    = 
            `<div class="row prod" id="prod_${index}">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="form-group field-quote-product-${index} required">
                        <label class="control-label" for="quote-product-${index}">Prodotto</label>
                        <select id="quote-product-${index}" class="form-control" name="Quote[product][${index}]" onchange="getProductInfo(${index})" aria-required="true">
                            <option value="">Scegli</option>
                            <option value="3">[U]classic</option>
                            <option value="4">[U]gliarulo</option>
                            <option value="2">[U]live</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-md-1 col-sm-4 col-12">
                    <div class="form-group field-quote-amount-${index} required has-error">
                        <label class="control-label" for="quote-amount-${index}">Quantità</label>
                        <input type="text" id="quote-amount-${index}" class="form-control" name="Quote[amount][${index}]" onchange="updateTotalPrice(value)" aria-required="true" aria-invalid="true">

                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 col-12">
                    <div class="form-group field-quote-color-${index} required">
                        <label class="control-label" for="quote-color-${index}">Colore</label>
                        <select id="quote-color-${index}" class="form-control" name="Quote[color][${index}]" aria-required="true">
                            <option value="">Scegli</option>
                            <option value="1">Light blue</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-12">
                    <div class="form-group field-quote-custom_color-0">
                        <label class="control-label" for="quote-custom_color-0">Colore custom</label>
                        <input type="text" id="quote-custom_color-0" class="form-control" name="Quote[custom_color][0]" maxlenght="">

                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="form-group field-quote-packaging required">
                        <label class="control-label" for="quote-packaging">Confezione</label>
                        <select id="quote-packaging" class="form-control" name="Quote[packaging][${index}]" aria-required="true">
                            <option value="">Scegli</option>
                            <option value="3">Scatola </option>
                            <option value="2">Scatola con Raso</option>
                            <option value="4">senza scatola </option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group field-quote-product-${index} required">
                        <label class="control-label" for="quote-product-${index}"></label>
                        <div class="text-md" style="cursor:pointer" onclick="removeProductLine(${index})"><i style="margin-top:17px; margin-left:7px; color:red" class="fas fa-minus-circle" ></i></div>
                    </div>
                </div>
            </div>
        `;
        
        $(node[node.length -1]).after(html); //append to latest row
            
    }
</script>