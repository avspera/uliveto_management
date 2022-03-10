<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\QuoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_number') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'updated_at') ?>

    <?= $form->field($model, 'id_client') ?>

    <?php // echo $form->field($model, 'product') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'color') ?>

    <?php // echo $form->field($model, 'packaging') ?>

    <?php // echo $form->field($model, 'placeholder') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'total') ?>

    <?php // echo $form->field($model, 'deposit') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <?php // echo $form->field($model, 'shipping') ?>

    <?php // echo $form->field($model, 'deadline') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
