<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\InviaCatalogo */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Carica nuovi file';
$this->params['breadcrumbs'][] = ['label' => 'Invio cataloghi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="invia-catalogo-form">
    <div class="card card-info">
        <div class="card-header"><div class="text-lg">Carica i file</div></div>
        <div class="card-body table-responsive">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
                <div class="row">
                    <div class="col-12">
                        <?= FileInput::widget([
                            'model' => $model,
                            'attribute' => 'files[]',
                            'options' => ['multiple' => true, 'accept' => ["png", "jpg", 'pdf']]
                        ]);?>
                    </div>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Carica', ['class' => 'btn btn-success']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
