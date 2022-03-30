<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\date\DatePicker;
$prefix_url = Yii::getAlias("@web");
?>
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number] {
    -moz-appearance: textfield;
    }
</style>

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
                <div class="col-md-2 col-sm-6 col-12">
                    <div class="form-group field-quote-product-0 required">
                        <input type="hidden" id="productSubtotal-0" name="productSubtotal-0" value=0>
                        <label class="control-label" for="quote-product-0">Prodotto</label>
                        <select id="quote-product-0" class="form-control" name="Quote[product][0]" onchange="enableFields(0)" aria-required="true">
                            <option value="">Scegli</option>
                            <?php foreach($products as $product) { ?>
                                <option price="<?= $product->price ?>" value="<?= $product->id ?>"><?= $product->name." - ".$product->formatNumber($product->price) ?> </option>
                            <?php } ?>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-12" style="display:inline-block">
                    <div class="form-group field-quote-amount-0">
                        <label class="control-label" for="quote-amount-0">Quantità</label>
                        <div class="input-group inline-group">
                            <!-- <div class="input-group-prepend">
                                <button onclick="changeAmount(0, 'detract')" class="btn btn-warning btn-minus">
                                <i class="fa fa-minus"></i>
                                </button>
                            </div> -->
                            <input type="number" min="1" id="quote-amount-0" class="form-control" readonly name="Quote[amount][0]" prevValue=0 onchange="manualChangeAmount(0)" value="0">
                            <!-- <div class="input-group-append">
                                <button onclick="changeAmount(0, 'add')" class="btn btn-success btn-plus">
                                <i class="fa fa-plus"></i>
                                </button>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6 col-12">
                    <?= $form->field($model, 'color[0]')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Color::find()->orderBy('label')->all(), 'id', 'label'), ['prompt' => 'Scegli', 'disabled' => true])->label('Colore'); ?>
                </div>
                <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'custom_color[0]')->textInput(["maxlenght" => true, "readonly" => true]) ?></div>
                <div class="col-md-2 col-sm-6 col-12">
                    <div class="form-group field-quote-id_packaging-0 required">
                        <label class="control-label" for="quote-id_packaging-0">Confezione</label>
                        <select disabled id="quote-id_packaging-0" class="form-control" name="Quote[id_packaging][0]" onchange="addPackagingPrice(0)" aria-required="true">
                            <option price="" value="">Scegli</option>
                            <?php foreach($packagings as $packaging) { ?>
                                <option price="<?= $packaging->price ?>" value="<?= $packaging->id ?>"><?= $packaging->label." - ".$packaging->formatNumber($packaging->price) ?> </option>
                            <?php } ?>
                        </select>
                        <div class="help-block"></div>
                    </div>
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
                <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'confetti_omaggio')->checkBox(["onChange" => "removePrezzoConfetti(value)"]); ?></div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'custom_amount')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-9 col-sm-12 col-12"><?= $form->field($model, 'custom')->textarea(['rows' => 6]) ?></div>    
            </div>
        </div>
    </div>

  

    <div class="card card-secondary">
        <div class="card-header">
            <div class="text-lg">Costi</div>    
        </div>
        <div class="card-body table-responsive">

            <div class="row">
                <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'id_sconto')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Sales::find()->orderBy('name')->all(), 'id', 'name'), ['prompt' => 'Scegli', "onChange" => "applySales(value)", 'disabled' => true]); ?></div>
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

    <div class="card card-secondary">
        <div class="card-header">
            <div class="text-lg">Altro</div>    
        </div>
        <div class="card-body table-responsive">
            <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
<?php $prefix_url = Yii::getAlias("@web"); ?>
<script src="<?= $prefix_url ?>/js/quote.js"></script>

<script>


function enableFields(index){
    $("#quote-amount-"+index).removeAttr("readonly");
    $("#quote-color-"+index).removeAttr("disabled");
    $("#quote-custom_color-"+index).removeAttr("readonly");
    $("#quote-id_packaging-"+index).removeAttr("disabled");
    $("#quote-id_sconto-"+index).removeAttr("disabled");
    $("#quote-id_sconto").removeAttr("disabled");
}

function manualChangeAmount(index){
    var prev = $(`#quote-amount-${index}`).attr("prevValue");;
    prev    = Number.isNaN(prev) ? 0 : parseInt(prev);
    var current = $(`#quote-amount-${index}`).val();
    current = Number.isNaN(current) ? 0 : parseInt(current);
    
        let currentTotalNoVat   = $('#quote-total_no_vat').val();
        currentTotalNoVat       = Number.isNaN(currentTotalNoVat) ? 0 : parseFloat(currentTotalNoVat)
        let price               = parseFloat($('#quote-product-'+index+" option:selected").attr("price"));
        let subtotal            = 0;
        
        if(prev < current){
            let difference      = parseInt(current-prev);
            subtotal        = parseFloat(difference*price);
            currentTotalNoVat   = currentTotalNoVat + subtotal;
        }else{
            let difference      = parseInt(prev-current);
            subtotal        = parseFloat(difference*price);
            currentTotalNoVat   = currentTotalNoVat - subtotal;
        }

        currentTotalNoVat = Math.abs(currentTotalNoVat)
        
        $("#productSubtotal-"+index).val(currentTotalNoVat);

        let newTotalWithVat = currentTotalNoVat + parseFloat(currentTotalNoVat / 100) * 4;
        
        $('#quote-total_no_vat').val(currentTotalNoVat.toFixed(2));
        $('#quote-total').val(parseFloat(newTotalWithVat).toFixed(2)); 
        $(`#quote-amount-${index}`).attr("prevValue", current);
    
}


function subtractDeposit(){
    let deposit         = $('#quote-deposit').val();
    let currentTotal    = $('#quote-total').val();
    let balance         = currentTotal-deposit;
    $('#quote-balance').val(parseFloat(balance).toFixed(2))
}

function addPackagingPrice(index){
    let price   = $(`#quote-id_packaging-${index} option:selected`).attr("price");
    let amount  = $("#quote-amount-"+index).val();
    let currentTotalNoVat   = $("#quote-total_no_vat").val();
    let currentTotal        = $("#quote-total").val();
    let totalNoVat          = 0;
    let newTotalNoVat       = 0;
    let newTotal            = 0;
    let subtotalPackaging   = parseInt(amount) * parseFloat(price);

    if (Number.isNaN(currentTotalNoVat)) {
        currentTotalNoVat = 0;
    }else{
        currentTotalNoVat = parseFloat(currentTotalNoVat);
    }

    if (Number.isNaN(currentTotal)) {
        currentTotal = 0;
    }else{
        currentTotal = parseFloat(currentTotal);
    }
    
    if(price == ""){
        newTotalNoVat   = currentTotalNoVat - subtotalPackaging;
        newTotal        = currentTotal - subtotalPackaging;
    }else{
        newTotalNoVat   = currentTotalNoVat + subtotalPackaging;
        newTotal        = currentTotal + subtotalPackaging;
    }

    $("#quote-total_no_vat").val(newTotalNoVat.toFixed(2));
    $("#quote-total").val(newTotal.toFixed(2));
}
function applySales(value){
    $.ajax({
        url: '<?= $prefixUrl ?>/web/sales/get-by-id',
        type: 'get',
        dataType: 'json',
        'data': {
            'id': value,
        },
        success: function (data) {
            let alertClass  = "alert-warning";
            let alertMsg    = "Ops...something wrong here. [PAY-101]";
            let subtotal    = 0;
            let amount      = 0;
            let sumPrice    = 0;
            if(data.status == "200")
            {
                let sale = parseInt(data.amount);
                let currentTotal = $("#quote-total_no_vat").val();
                parseFloat(currentTotal);
                console.log("currentTotalBeforeSale", currentTotal);
                $("input[id^='productSubtotal-']").each(function() {
                    let currentValue    = $(this).val();
                    console.log("currentValue", currentValue)
                    currentValue        = Number.isNaN(currentValue) ? 0 : parseFloat(currentValue)
                    sumPrice            += currentValue 
                });
                
                //price sum of all products
                subtotal    = sumPrice
                console.log("price sum of all bottles ", subtotal)
                //subtract from currentTotal (where might be additional services prices)
                let newTotalNoVat   = (currentTotal - subtotal)
                console.log("sottrai da currentTotal il prezzo delle bottiglie", newTotalNoVat)
                subtotal            = subtotal * ((100-sale) / 100) //apply sale
                console.log("apply sale", subtotal)
                //update current total by adding the saled price to services prices in currentTotal
                newTotalNoVat = Math.abs(parseFloat(newTotalNoVat) + subtotal)
                console.log("new totalNoVat", newTotalNoVat)
                
                $("#quote-total_no_vat").val(newTotalNoVat.toFixed(2));
                
                applyIvaToTotal(newTotalNoVat)
                alertClass  = "alert-success";
                alertMsg    = `Hai applicato lo sconto del ${sale}%. Era ${currentTotal} &euro;`;

                let html = `
                    <div style="margin-top: 5px;" class="alert ${alertClass}">
                        ${alertMsg}
                    </div>
                `

                $("#quote-total_no_vat").siblings('div:first').html(html)
            }

            
            $(".alert").addClass(alertClass);
            $(".alert").show()
        }
    });
}

function removeProductLine(index){
    let id = `#prod_${index}`;
    let price   = $('#quote-product-'+index+" option:selected").attr("price");
    let amount  = $("#quote-amount-"+index).val();
    let currentTotal =  $("#quote-total_no_vat").val();
    parseFloat(currentTotal);
    let newTotalNoVat = currentTotal-parseFloat(price*amount);
    let newTotalWithVat = applyIvaToTotal(newTotalNoVat)
    $('#quote-total_no_vat').val(newTotalNoVat.toFixed(2));
    $('#quote-total').val(newTotalWithVat.toFixed(2)); 
    $(id).remove()
}

function applyIvaToTotal(newTotalNoVat){
    let out = newTotalNoVat + (newTotalNoVat / 100) * 4;
    $('#quote-total').val(out.toFixed(2)); 
    return out;
}

function calculatePrezzoConfetti(price){
    let subtotal = 0;
    let amount   = 0; 
    $('input[id^="quote-amount-"]').each(function() {
        amount      += $(this).val();
        amount      = Number.isNaN(amount) ? 0 : parseInt(amount)
        subtotal    = parseFloat(amount*price)
    });

    return subtotal;
}
//FIX THIS
function addPrezzoConfetti (price) {
    let subtotal        = this.calculatePrezzoConfetti(price)
    let currentTotal    = $('#quote-total_no_vat').val();
    currentTotal        = Number.isNaN(currentTotal) ? 0 : parseFloat(currentTotal)
    let newTotalNoVat   = currentTotal+subtotal;
    $('#quote-total_no_vat').val(newTotalNoVat.toFixed(2))
    applyIvaToTotal(newTotalNoVat); 
}

function removePrezzoConfetti (price) {
    let subtotal        = calculatePrezzoConfetti(price)
    let currentTotal    = $('#quote-total_no_vat').val();
    currentTotal        = Number.isNaN(currentTotal) ? 0 : parseFloat(currentTotal)
    let newTotalNoVat   = Math.abs(currentTotal-subtotal);
    $('#quote-total_no_vat').val(newTotalNoVat)
    applyIvaToTotal(newTotalNoVat); 
}

function addCustomAmount (price) {
    let currentTotal    = $('#quote-total_no_vat').val();
    let newTotalNoVat   = currentTotal+price;
    $('#quote-total_no_vat').val(parseFloat(newTotalNoVat).toFixed(2))
    applyIvaToTotal(newTotalNoVat);
}

function addProductLine(){
    let index   = $("div[id^='prod_']").attr("id");
    let node    = $("div[id^='prod_']");
    index       = index.substr(index.indexOf("_")+1, 1);
    index       = parseInt(index+1)
    
    let html = 
        `<div class="row prod" id="prod_${index}">
            <div class="col-md-2 col-sm-6 col-12">
                <div class="form-group field-quote-product-${index} required">
                    <input type="hidden" id="productSubtotal-${index}" name="productSubtotal-${index}" value=0>
                    <label class="control-label" for="quote-product-${index}">Prodotto</label>
                    <select id="quote-product-${index}" class="form-control" name="Quote[product][${index}]" onchange="enableFields(${index})" aria-required="true">
                        <option value="">Scegli</option>
                        <?php foreach($products as $product) { ?>
                            <option price="<?= $product->price ?>" value="<?= $product->id ?>"><?= $product->name." - ".$product->formatNumber($product->price) ?></option>
                        <?php } ?>
                    </select>
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 col-12" style="display:inline-block">
                <div class="form-group field-quote-amount-${index}">
                    <label class="control-label" for="quote-amount-${index}">Quantità</label>
                    <div class="input-group inline-group">
                        <input readonly type="number" min="1" id="quote-amount-${index}" readonly class="form-control" name="Quote[amount][${index}]" prevValue=0 onchange="manualChangeAmount(${index})" value="0">
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 col-12">
                <div class="form-group field-quote-color-${index} required">
                    <label class="control-label" for="quote-color-${index}">Colore</label>
                    <select disabled id="quote-color-${index}" class="form-control" name="Quote[color][${index}]" aria-required="true">
                        <option value="">Scegli</option>
                        <option value="1">Light blue</option>
                    </select>
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-12">
                <div class="form-group field-quote-custom_color-${index}">
                    <label class="control-label" for="quote-custom_color-${index}">Colore personalizzato</label>
                    <input readonly type="text" id="quote-custom_color-${index}" class="form-control" name="Quote[custom_color][${index}]" maxlenght="">

                    <div class="help-block"></div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 col-12">
                <div class="form-group field-quote-packaging required">
                    <label class="control-label" for="quote-packaging">Confezione</label>
                    <select disabled="" id="quote-id_packaging-${index}" class="form-control" name="Quote[id_packaging][${index}]" onchange="addPackagingPrice(${index})" aria-required="true">
                        <option price="" value="">Scegli</option>
                        <?php foreach($packagings as $package) { ?>
                            <option price="<?= $package->price ?>" value="<?= $package->id ?>"><?= $package->label." - ".$package->formatNumber($package->price) ?></option>
                        <?php } ?>
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