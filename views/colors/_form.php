<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\Color */
/* @var $form yii\widgets\ActiveForm */
$preview = [];
$i = 0;
if(!empty($model->picture)){
    
    $preview = [
        "caption" => "File",
        'width' => "200px",
        'url' => Url::to(["colors/delete-attachment?id=".$model->id]),
        'key' => $model->picture,
        "id" => $model->id
    ];

}

?>

<div class="color-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div class="row">
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'content')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4 col-sm-4 col-12"><?= $form->field($model, 'id_product')->dropdownlist(yii\helpers\ArrayHelper::map(app\models\Product::find()->orderBy('name')->all(), 'id', 'name'), ['prompt' => 'Scegli'])->label('Prodotto'); ?></div>
    </div>

    <div class="row">
        <div class="col-12">
            <?= FileInput::widget([
                'model' => $model,
                'attribute' => 'picture',
                'options' => ['multiple' => false, 'accept' => ["png", "jpg"]],
                'pluginOptions' => [
                    'initialPreview'=>[
                        !empty($model->picture) ? Html::img($model->picture, ["width " => "200px"]) : []
                    ],
                    'overwriteInitial' => false,
                    'deleteUrl'=> Url::to(['/colors/delete-attachment?id='.$model->id]),
                ],
            ]);?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
