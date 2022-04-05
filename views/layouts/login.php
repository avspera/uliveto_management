<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\LoginForm;

\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
\hail812\adminlte3\assets\AdminLteAsset::register($this);
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');

$model = new LoginForm();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title>Accedi a Manager - OrciCilento</title>
    <?php $this->head() ?>
</head>
<body id="page-top" class="bg-gradient-info">
<?php $this->beginBody() ?>

    <div class="container" style="margin-top: 20px">
        <div class="d-flex justify-content-center">
            <img src="<?=Yii::getAlias("@web")?>/images/logo_white.png" alt="L'uliveto" class="brand-image">
        </div>

        <div class="card" style="margin-top: 1rem; width: 500px; margin: 0 auto;float: none; margin-top: 20px">
            <div class="card-body table-responsive login-card-body">
    
                <p class="login-box-msg">Accedi per iniziare una nuova sessione</p>

                <?php $form = \yii\bootstrap4\ActiveForm::begin(['id' => 'login-form']) ?>

                <?= $form->field($model,'username', [
                    'options' => ['class' => 'form-group has-feedback'],
                    'inputTemplate' => '{input}<div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>',
                    'template' => '{beginWrapper}{input}{error}{endWrapper}',
                    'wrapperOptions' => ['class' => 'input-group mb-3']
                ])
                    ->label(false)
                    ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

                <?= $form->field($model, 'password', [
                    'options' => ['class' => 'form-group has-feedback'],
                    'inputTemplate' => '{input}<div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>',
                    'template' => '{beginWrapper}{input}{error}{endWrapper}',
                    'wrapperOptions' => ['class' => 'input-group mb-3']
                ])
                    ->label(false)
                    ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4">
                        <?= Html::submitButton('Entra', ['class' => 'btn btn-info btn-block']) ?>
                    </div>
                </div>

                <?php \yii\bootstrap4\ActiveForm::end(); ?>

                <p class="mb-1">
                    <a style="color: orange" href="<?= Url::to(["site/request-password-reset"]) ?>">Ho dimenticato la password</a>
                </p>
                <!-- <p class="mb-0">
                    <a href="register.html" class="text-center">Register a new membership</a>
                </p> -->
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>