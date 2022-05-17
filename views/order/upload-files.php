<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

$this->title = $model->order_number." - ".$model->getClient().": Carica allegati";
$this->params['breadcrumbs'][] = ['label' => 'Ordini', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$preview = [];
$i = 0;
if(!empty($model->attachments)){
    foreach($model->attachments as $attachmet){
        $preview[$i] = [
            "caption" => "File",
            'width' => "200px",
            'url' => Url::to(["order/delete-attachment?id_quote=".$model->id]),
            'key' => $attachmet,
            "id_quote" => $model->id
        ];
        $i++;
    }
}

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
                    'options' => ['multiple' => true, 'accept' => ["png", "jpg", "pdf"]]
                ]);?>
            </div>

            <div class="form-group" style="margin-top: 20px">
                <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
            </div>

        </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function() {
    $('#quote-attachments').fileinput({
        overwriteInitial: false,
        validateInitialCount: true,
    }).on('filebeforedelete', function() {
        var aborted = !window.confirm('Are you sure you want to delete this file?');
        if (aborted) {
            window.alert('File deletion was aborted!');
        };
        return aborted;
    }).on('filedeleted', function() {
        setTimeout(function() {
            window.alert('File deletion was successful!');
        }, 900);
    });
});
</script>