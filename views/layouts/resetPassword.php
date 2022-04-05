<?php
use yii\helpers\Html;
use app\models\LoginForm;
use yii\widgets\ActiveForm;

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
<body id="page-top" class="bg-gradient-warning">
<?php $this->beginBody() ?>

    <div class="container" style="margin-top: 20px">
        <div class="d-flex justify-content-center">
            <img src="<?=Yii::getAlias("@web")?>/images/logo_white.png" alt="L'uliveto" class="brand-image">
        </div>

        
        <div class="card" style="margin-top: 1rem; width: 500px; margin: 0 auto;float: none; margin-top: 20px">
            <div class="card-body table-responsive login-card-body">
            <div class="error-content" style="margin-left: auto;">
        <h3><i class="fas fa-exclamation-triangle text-warning"></i> <?= Html::encode($name) ?></h3>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="sign_up">
                    <!-- /head -->
                    <div class="main">
                        <p>Ciao. Inserisci la tua nuova password</p>
                        <?php if(Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-warning alert-dismissible" style="color: white">
                            <?php echo Yii::$app->session->getFlash('error'); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php endif; ?> 

                        <?php if(Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <?php echo Yii::$app->session->getFlash('success'); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php endif; ?> 
                        <?php $form = ActiveForm::begin(['method' => 'post']); ?>

                            <?= $form->field($model, 'password')->passwordInput(['autofocus' => true, 'placeholder' => "Almeno 6 caratteri"]) ?>
                            <div class="form-group">
                                <?= Html::submitButton('Procedi', ['class' => 'btn btn-success']) ?>
                            </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>