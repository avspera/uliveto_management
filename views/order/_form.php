<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\Client;
use yii\web\JsExpression;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\grid\ActionColumn;

$prefix_url     = Yii::getAlias("@web");
$placeholders   = \app\models\Segnaposto::find()->all();
$client         = Client::find()->select(["id", "name", "surname"])->where(["id" => $model->id_client])->one();
$clientData     = !empty($client) ? $client->name." ".$client->surname : ""
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

        <div class="card card-info">
            <div class="card-header">
                <div class="row"><div class="text-lg">Dettaglio prodotti</div></div>
            </div>
            <div class="card-body table-responsive">
                
                <?= GridView::widget([
                    'dataProvider' => $quoteDetails,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'id_product',
                            'value' => function($model){
                                return $model->getProduct();
                            },
                        ],
                        [
                            'attribute' => 'id_color',
                            'value' => function($model){
                                return !empty($model->id_color) ? $model->getColor() : "";
                            },
                        ],
                        [
                            'attribute' => "custom_color",
                        ],
                        [
                            'attribute' => 'id_packaging',
                            'value' => function($model){
                                return $model->getPackaging();
                            },
                        ],
                        'amount',
                        [ 
                            'class' => ActionColumn::className(),
                            'template' => "{delete}",
                            'buttons' => [
                                'delete' => function ($url, $model) {
                                    return Html::a(
                                        '<span class="fas fa-trash"></span>',
                                        Url::to(["quote-details/delete", "id" => $model->id]), 
                                        [
                                            'title' => 'Cancella',
                                            'data-pjax' => '0',
                                        ]
                                    );
                                },
                            ],
                        ]
                    ]
                ]); ?>
                
            </div>
        </div>

        <div class="card card-success">
            <div class="card-header">
                <div class="row">
                    <div class="text-lg">Aggiungi prodotti</div>    
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
                                <input type="number" min="1" id="quote-amount-0" class="form-control" readonly name="Quote[amount][0]" prevValue=0 onchange="manualChangeAmount(0)" value="0">
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
                            <select prevPrice=0 disabled id="quote-id_packaging-0" class="form-control" name="Quote[packaging][0]" onchange="addPackagingPrice(0)" aria-required="true">
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
                    <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'confetti')->dropdownlist([0 => "NO", 1 => "SI"], ['onchange' => "enableConfettiFields(value)"]) ?></div>
                    <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'prezzo_confetti')->textInput(['maxlength' => true, "onchange" => "addPrezzoAggiuntivo(value)", "readonly" => true, "type" => "number", "prevValue" => 0]); ?></div>
                    <div class="col-md-3 col-sm-4 col-12"><?= $form->field($model, 'confetti_omaggio')->radio(["onChange" => "removePrezzoAggiuntivo('confetti')", "disabled" => true]); ?></div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-sm-4 col-12">
                        <div class="form-group field-quote-custom_amount">
                            <label class="control-label" for="quote-custom_amount">Costo personalizzazione</label>
                            <input prevValue=0 onchange="value == 0 ? removePrezzoAggiuntivo('custom_amount') : addPrezzoAggiuntivo(value, 'custom_amount')" type="number" id="quote-custom_amount" class="form-control" name="Quote[custom_amount]">

                            <div class="help-block"></div>
                        </div>
                    </div>
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
                    <div class="col-md-4 col-sm-4 col-12">
                        <?= $form->field($model, 'total_no_vat')->textInput(['maxlength' => true, "readonly" => true]) ?>
                    </div>
                    <div class="col-md-4 col-sm-4 col-12">
                        <?= $form->field($model, 'total')->textInput(['maxlength' => true, "readonly" => true])->label('Totale <i onclick="editTotal()" style="color: orange; cursor:pointer" class="fas fa-pencil-alt"></i>') ?>
                    </div>
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
            </div>
        
        </div>

        <div class="card card-secondary">
            <div class="card-header">
                <div class="text-lg">Altro</div>    
            </div>
            <div class="card-body table-responsive">
                <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>
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
</div>
<?php $prefix_url = Yii::getAlias("@web"); ?>
<script src="<?= $prefix_url ?>/js/quote.js"></script>

<script>

    
        function getPrevSelectValue() {
            let out;
            $('select').on('focusin', function(){
                $(this).data('val', $(this).val());
            });

            $('select').on('change', function(){
                var prev = $(this).data('val');
                var current = $(this).val();
                out.prev = prev
                out.current = current
                console.log("Prev value " + prev);
                console.log("New value " + current);
            });
            return out;
        }   

    function enableFields(index){
        $("#quote-amount-"+index).removeAttr("readonly");
        $("#quote-color-"+index).removeAttr("disabled");
        $("#quote-custom_color-"+index).removeAttr("readonly");
        $("#quote-id_packaging-"+index).removeAttr("disabled");
        $("#quote-id_sconto-"+index).removeAttr("disabled");
        $("#quote-id_sconto").removeAttr("disabled");
    }

function editTotal(){
    let element = $("#quote-total");
    if(element.attr("readonly"))
        $("#quote-total").removeAttr("readonly");
    else{
        let currentTotal       = element.val();
        currentTotal           = isNaN(currentTotal) ? 0 : parseFloat(currentTotal)
        element.attr("readonly", true)
    }
}

function enableConfettiFields(value){
    if(value){
        $("#quote-prezzo_confetti").removeAttr("readonly");
        $("#quote-confetti_omaggio").removeAttr("disabled");
    }else{
        $("#quote-prezzo_confetti").attr("readonly", true);
        $("#quote-confetti_omaggio").attr("disabled", true);
    }
}

function manualChangeAmount(index){
    var prev = $(`#quote-amount-${index}`).attr("prevValue");
    prev    = isNaN(prev) ? 0 : parseInt(prev);
    var current = $(`#quote-amount-${index}`).val();
    current = isNaN(current) ? 0 : parseInt(current);
    
    let currentTotalNoVat   = $('#quote-total_no_vat').val();
    currentTotalNoVat       = isNaN(currentTotalNoVat) ? 0 : parseFloat(currentTotalNoVat)
    let price               = parseFloat($('#quote-product-'+index+" option:selected").attr("price"));
    let subtotal            = 0;
    
    if(prev < current){
        let difference      = parseInt(current-prev);
        subtotal            = parseFloat(difference*price);
        currentTotalNoVat   = currentTotalNoVat + subtotal;
    }else{
        let difference      = parseInt(prev-current);
        subtotal            = parseFloat(difference*price);
        currentTotalNoVat   = currentTotalNoVat - subtotal;
    }

    currentTotalNoVat = Math.abs(currentTotalNoVat)
    
    $("#productSubtotal-"+index).val(subtotal);

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

function calculateNewTotal(prevValue, price, amount) {
    let subtotalPackaging   = 0;
    let newTotalNoVat       = 0;
    let currentTotalNoVat   = $("#quote-total_no_vat").val();

    if (isNaN(currentTotalNoVat)) {
        currentTotalNoVat = 0;
    }else{
        currentTotalNoVat = parseFloat(currentTotalNoVat);
    }
    
    if(prevValue == 0){
        subtotalPackaging   = parseInt(amount) * parseFloat(price);
        newTotalNoVat       = currentTotalNoVat + subtotalPackaging;
    }else{
        if(prevValue < price){
            let difference = price - prevValue
            subtotalPackaging  = parseInt(amount) * parseFloat(difference);
            newTotalNoVat   = Math.abs(currentTotalNoVat+subtotalPackaging);
        }else{
            let difference      = Math.abs(prevValue - price);
            subtotalPackaging   = parseInt(amount) * parseFloat(difference);
            newTotalNoVat       = Math.abs(currentTotalNoVat-subtotalPackaging);
        }
    }
    
    $("#quote-total_no_vat").val(newTotalNoVat.toFixed(2));
    applyIvaToTotal(newTotalNoVat)
}

function addPackagingPrice(index){
    
    let price       = $(`#quote-id_packaging-${index} option:selected`).attr("price");
    let prevValue   = $(`#quote-id_packaging-${index}`).attr("prevPrice");
    prevValue       = isNaN(prevValue) ? 0 : parseFloat(prevValue)
    $(`#quote-id_packaging-${index}`).attr("prevPrice", price) //update prev value
    let amount              = $("#quote-amount-"+index).val();
    calculateNewTotal(prevValue, price, amount);

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
            let sumBottles  = 0;
            let sumPrices   = 0;
            if(data.status == "200")
            {
                let sale = parseInt(data.amount);
                let currentTotal = $("#quote-total_no_vat").val();
                currentTotal = isNaN(currentTotal) ? 0 : parseFloat(currentTotal); 
                console.log("currentTotalBeforeSale", currentTotal);
                console.log("sum prices of all bottles", sumBottles)
                sumPrices   = calculateSumProductsPrice()
                subtotal    = sumPrices
                let currentTotalNoBottles = currentTotal - sumPrices;
                let percentage      = parseFloat((subtotal*sale)/100)
                console.log("percentage", percentage)
                subtotal        = subtotal - percentage
                console.log("subtotal", subtotal);
                let newTotalNoVat   = subtotal + currentTotalNoBottles;
                newTotalNoVat = Math.abs(parseFloat(newTotalNoVat))
                
                console.log("new totalNoVat", newTotalNoVat)
                
                $("#quote-total_no_vat").val(newTotalNoVat.toFixed(2));
                
                applyIvaToTotal(newTotalNoVat)

                alertClass  = "alert-success";
                alertMsg    = `Hai applicato lo sconto del ${sale}%. Era ${currentTotal} &euro;`;

                let html = `
                    <div style="margin-top: 5px;" class="alert ${alertClass} alert-dismissible">
                        ${alertMsg}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
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
    price       = isNaN(price) ? 0 : parseFloat(price)
    let amount  = $("#quote-amount-"+index).val();
    amount      = isNaN(amount) ? 0 : parseInt(amount)
    if(amount == 0) 
        {
            $(id).remove(); 
            return
        }
    let currentTotal    =  $("#quote-total_no_vat").val();
    currentTotal        = isNaN(currentTotal) ? 0 : parseFloat(currentTotal);
    let subtotal        = price*amount;
    let newTotalNoVat   = Math.abs((currentTotal-subtotal));
    $('#quote-total_no_vat').val(newTotalNoVat.toFixed(2));
    let newTotalWithVat = applyIvaToTotal(newTotalNoVat)
    $(id).remove()
}

function applyIvaToTotal(newTotalNoVat){
    let out = (newTotalNoVat + (newTotalNoVat / 100) * 4);
    out     = isNaN(out) ? 0 : Math.abs(parseFloat(out).toFixed(2));
    $('#quote-total').val(out);
    return out;
}

function calculatePrezzoAggiuntivo(price){
    let subtotal = 0;
    let amount   = 0;
    let prevBottleAmount = "<?= $currentBottleAmount ?>";
    prevBottleAmount = isNaN(prevBottleAmount) ? 0 : parseFloat(prevBottleAmount);

    $('input[id^="quote-amount-"]').each(function() {
        amount      = $(this).val();
        amount      = isNaN(amount) ? 0 : parseInt(amount)  
        subtotal    += amount 
    });
    
    //if is first time, get prev bottle amount (quotedetails)
    if(subtotal == 0){
        subtotal = currentBottleAmount
    }else{
        subtotal += prevBottleAmount
    }

    subtotal    = parseFloat(subtotal*price)

    return subtotal;
}

function addPrezzoAggiuntivo (price, target= "") {
    
    let currentTotal    = $('#quote-total_no_vat').val();
    currentTotal        = isNaN(currentTotal) ? 0 : parseFloat(currentTotal)
      
    if(isNaN(price)){
        price = $('#quote-placeholder').find(':selected').attr('price');
        price = isNaN(price) ? 0 : parseFloat(price)
    }
    
    if(target == "segnaposto"){
        let segnapostoTotal = this.calculatePrezzoAggiuntivo(price)
        console.log("segnapostoTotal", segnapostoTotal);
        currentTotal += segnapostoTotal;
        console.log("new total", currentTotal);
        $('#quote-total_no_vat').val(currentTotal.toFixed(2))
        applyIvaToTotal(currentTotal); 

        return;
    }

    let prev;
    let current;
    
    if(target == "custom_amount"){
        prev    = $(`#quote-custom_amount`).attr("prevValue");
        current = $(`#quote-custom_amount`).val();
    }else{
        prev    = $(`#quote-prezzo_confetti`).attr("prevValue");
        current = $(`#quote-prezzo_confetti`).val();
    }

    prev    = isNaN(prev) ? 0 : parseInt(prev);
    current = isNaN(current) ? 0 : parseInt(current);
    
    if(target == "custom_amount"){
        $(`#quote-custom_amount`).attr("prevValue", current)
    }
    else{
        $(`#quote-prezzo_confetti`).attr("prevValue", current)
    }

    
    if(prev == 0) 
        currentTotal = currentTotal 
    
    let subtotal = this.calculatePrezzoAggiuntivo(price)
    if(prev < current){
        currentTotal = Math.abs(currentTotal+subtotal);
    }else{
        currentTotal = Math.abs(currentTotal-subtotal);
    }

    $('#quote-total_no_vat').val(currentTotal.toFixed(2))
    applyIvaToTotal(currentTotal); 
}

function calculateSumProductsPrice(){
    let sumProducts = 0;
    $("input[id^='productSubtotal-']").each(function() {
        let currentValue    = $(this).val();
        currentValue        = isNaN(currentValue) ? 0 : parseFloat(currentValue)
        sumProducts            += currentValue 
    });

    return sumProducts;
}
function calculateSumProducts() {
    let sumProducts = 0;
    $("input[id^='quote-amount-']").each(function() {
        let currentValue    = $(this).val();
        currentValue        = isNaN(currentValue) ? 0 : parseFloat(currentValue)
        sumProducts            += currentValue 
    });

    return sumProducts;
}

function removePrezzoAggiuntivo (target) 
{   
    let price           = target == "confetti" ? $('#quote-prezzo_confetti').val() : $('#quote-custom_amount').val();
    price               = isNaN(price) ? 0 : parseFloat(price)
    if(price == 0 && target == "custom_amount"){
        price = $('#quote-custom_amount').attr("prevVal")
    }

    let currentTotal    = $('#quote-total_no_vat').val();
    console.log("currentTotal", currentTotal)
    currentTotal        = isNaN(currentTotal) ? 0 : parseFloat(currentTotal)
    let sumProducts     = calculateSumProducts();
    let subtotal        = sumProducts * price;
    console.log("subtotal", subtotal)
    let newTotalNoVat   = parseFloat(Math.abs(currentTotal-subtotal).toFixed(2));
    $('#quote-total_no_vat').val(newTotalNoVat);
    applyIvaToTotal(newTotalNoVat);
    $('#quote-prezzo_confetti').attr("readonly", true)
}


function addProductLine(){
    let index   = $("div[id^='prod_']").last().attr("id");
    let node    = $("div[id^='prod_']");
    index       = index.substr(index.indexOf("_")+1, 1);
    index       = parseInt(index)+1
    
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
                        <?php foreach($colors as $color) { ?>
                            <option value="<?= $color->id ?>"><?= $color->label ?></option>
                        <?php } ?>
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
                    <select disabled="" id="quote-id_packaging-${index}" class="form-control" name="Quote[packaging][${index}]" onchange="addPackagingPrice(${index})" aria-required="true">
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