<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\date\DatePicker;
use app\models\Segnaposto;


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
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="form-group field-quote-product-0 required">
                        <input type="hidden" id="productSubtotal-0" name="productSubtotal-0" value=0>
                        <label class="control-label" for="quote-product-0">Prodotto</label>
                        <select id="quote-placeholder-id_placeholder" class="form-control" name="QuotePlaceholder[id_placeholder]" aria-required="true">
                            <option value="">Scegli</option>
                            <?php foreach($placeholders as $placeholder) { ?>
                                <option price="<?= $placeholder->price ?>" value="<?= $placeholder->id ?>"><?= $placeholder->label." - ".$placeholder->formatNumber($placeholder->price) ?> </option>
                            <?php } ?>
                        </select>
                        <div class="help-block"></div>
                    </div>
                    
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <?= $form->field($model, 'amount')->textInput(["onchange" => "calculateTotal(value)"]) ?>
                </div>
                <div class="col-md-2 col-sm-4 col-12" style="display:inline-block">
                    <div class="form-group field-quote-placeholder-total">
                        <label class="control-label" for="quote-placeholder-total">Totale</label>
                        <div class="input-group inline-group">
                            <input type="number" min="1" id="quote-placeholder-total" class="form-control" readonly name="QuotePlaceholder[total]">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-12">
                    <?= $form->field($model, 'date_deposit')->widget(
                        DatePicker::classname(), [
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd',
                                        'language' => "it",
                                        'startDate' => date("Y-m-d")
                                ]
                        ]); 
                    ?>
                </div>
                <div class="col-md-6 col-sm-6 col-12">
                    <?= $form->field($model, "acconto")->textInput(["type" => "number","onchange" => "calculateSaldo(value)"])?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-12">
                    <?= $form->field($model, 'date_balance')->widget(
                        DatePicker::classname(), [
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd',
                                        'language' => "it",
                                        'startDate' => date("Y-m-d")
                                ]
                        ]); 
                    ?>
                </div>

                <div class="col-md-6 col-sm-6 col-12">
                    <?= $form->field($model, "saldo")->textInput(["type" => "number"])?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    function calculateTotal(amount){
        let price   = $("#quote-placeholder-id_placeholder option:selected").attr("price");
        price = isNaN(price) ? 0 : parseFloat(price);
        let total = amount * price;
        total = isNaN(total) ? 0 : parseFloat(total);
        let totalWithVat = applyIvaToTotal(total);
        $("#quote-placeholder-total").val(totalWithVat);
    }

    function calculateSaldo(acconto){
        let total = $("#quote-placeholder-total").val();
        total = isNaN(total) ? 0 : parseFloat(total);
        acconto = isNaN(acconto) ? 0 :  parseFloat(acconto)
        let saldo = parseFloat(total-acconto);
        
        $("#quoteplaceholder-saldo").val(saldo);
    }
 
    function applyIvaToTotal(newTotalNoVat){
        let out = (newTotalNoVat + (newTotalNoVat / 100) * 4);
        out     = isNaN(out) ? 0 : Math.abs(parseFloat(out).toFixed(2));
        $('#quote-total').val(out);
        return out;
    }
</script>
