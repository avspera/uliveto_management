<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\PasswordResetRequestForm;
use yii\bootstrap4\ActiveForm;

\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
\hail812\adminlte3\assets\AdminLteAsset::register($this);
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');

$model = new PasswordResetRequestForm();
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
                    <p>Inserisci il tuo indirizzo email per recuperare la password</p>
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

                    <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'email')->textInput(['autofocus' => true, "onchange" => "checkEmail(value)"]) ?>
                            <div id="error-container"></div>
                        </div>
                        <div class="form-group" style="margin-top:25px;">
                            <?= Html::submitButton('Invia', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>  
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>

    <script>
        function checkEmail(value){
            $.ajax({
                url: '<?= Url::to(['user/check-email']) ?>',
                type: 'get',
                dataType: 'json',
                'data': {
                    'email': value,
                },
                success: function (data) {
                    console.log("data.status", data.status);
                    if(data.status == "100"){
                        let html = `
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <span style="color: white">Questa email non è presente nei nostri database.</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `
                        $('#error-container').html(html)
                        $('button[type=submit]').attr("disabled", true)
                    }else{
                        $('#error-container').html("")
                        $('button[type=submit]').attr("disabled", false)
                    }
                },
                error: function(error){
                    console.log("error", error)
                }
            });
        }
    </script>

<?php $this->endBody() ?>
</body>


</html>
<?php $this->endPage() ?>