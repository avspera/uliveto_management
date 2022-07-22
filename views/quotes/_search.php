<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\jui\DatePicker;
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
                <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'order_number')->label("Numero ordine") ?></div>
                <div class="col-md-4 col-sm-4 col-12">
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
                <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'product')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Product::find()->orderBy('name')->all(), 'id', 'name'), ["prompt" => "Scegli"]) ?></div>
            </div>
            
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <label>Creato DA</label>
                 
                    <?= 
                        DatePicker::widget([
                            'name'  => 'QuoteSearch[start_date]',
                            'language' => 'it',
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => [
                                'class' => "form-control",
                                'autocomplete' => false
                            ],
                            'clientOptions' => [
                                'changeMonth' => true, 
                                'changeYear' => true,
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <label>Creato A</label>
                    <?= 
                        DatePicker::widget([
                            'name'  => 'QuoteSearch[end_date]',
                            'language' => 'it',
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => [
                                'class' => "form-control",
                                'autocomplete' => false
                            ],
                            'clientOptions' => [
                                'minDate' => 'today',
                                'changeMonth' => true, 
                                'changeYear' => true,
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <label>Consegna</label>
                    <?= 
                        DatePicker::widget([
                            'name'  => 'QuoteSearch[deadline]',
                            'language' => 'it',
                            'dateFormat' => 'yyyy-MM-dd',
                            'options' => [
                                'class' => "form-control",
                                'autocomplete' => false
                            ],
                            'clientOptions' => [
                                'minDate' => 'today',
                                'changeMonth' => true, 
                                'changeYear' => true,
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'from_web')->dropdownlist([0 => "NO", 1 => "SI"], ["prompt" => "Scegli"]) ?>
                </div>

            </div>
            
            <div class="row">
                <div class="form-group">
                    <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('<span>Cancella Filtri</span>', ['index'], ['class' => 'btn btn-outline-secondary'])?>
                </div>
            </div>

        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
