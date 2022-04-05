<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Request password reset';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    
    <div class="panel panel-default">
        <div class="panel-heading"><h2>Reset Password</h2></div>
        <div class="panel-body">
            <div class="main">
                <?php
                    $class = "";
                    if(Yii::$app->session->hasFlash('error')){
                        $class  = "background-granade";
                        $label  = "error";
                        
                    }else if(Yii::$app->session->hasFlash('error')){
                        $class="background-success";
                        $label = "success";
                    }
                    if(!empty($class)){
                    ?>
                    <div class="padded <?= $class ?>">
                        <?php echo Yii::$app->session->getFlash($label); ?>
                    </div>
                    <?php } ?>
                <p>Hai richiesto il reset della Password. Inserisci la tua email e riceverai le istruizioni per creare una nuova password</p>
                <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
                
                <div class="col-md-5"><?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?></div>
                    
                <div class="col-md-5">
                    <div class="form-group" style="margin-top:25px;">
                        <?= Html::submitButton('Invia', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
