<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

$this->title = $model->order_number." - ".$model->getClient().": Carica allegati";
$this->params['breadcrumbs'][] = ['label' => 'Ordini', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="upload-files-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div class="card">
        <div class="card-body">
            <div class="col-12">
                <?= FileInput::widget([
                    'model' => $model,
                    'attribute' => 'attachments',
                    'options' => ['multiple' => true, 'accept' => ["png", "jpg"]]
                ]);?>
            </div>

            <div class="form-group" style="margin-top: 20px">
                <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
            </div>

        </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
