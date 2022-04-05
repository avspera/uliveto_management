<?php 
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
?>


<div class="row justify-content-center">
    <div class="col-lg-4">
        <div class="sign_up">
            <div class="head background-granade">
                <div class="title"><h3>Nuova Password</h3></div>
            </div>
            <!-- /head -->
            <div class="main">
                <p>Ciao. Inserisci la tua nuova password</p>
                <?php $form = ActiveForm::begin(['id' => "reset-password-form"]); ?>
                <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>
                <div class="form-group">
                    <?= Html::submitButton('Procedi', ['class' => 'btn_1 full-width']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>