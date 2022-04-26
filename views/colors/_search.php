<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ColorSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="color-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'label') ?>

    <?= $form->field($model, 'content') ?>

    <?= $form->field($model, 'picture') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<span>Cancella Filtri</span>', ['index'], ['class' => 'btn btn-outline-secondary'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
