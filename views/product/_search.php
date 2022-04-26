<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'label') ?>

    <?= $form->field($model, 'image') ?>

    <?= $form->field($model, 'weight') ?>

    <?php // echo $form->field($model, 'id_packaging') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'capacity') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<span>Cancella Filtri</span>', ['index'], ['class' => 'btn btn-outline-secondary'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
