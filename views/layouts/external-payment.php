<?php
use yii\helpers\Html;
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
<body id="page-top" class="bg-gradient-warning">
<?php $this->beginBody() ?>

    <div class="container" style="margin-top: 20px">
        <div class="d-flex justify-content-center">
            <img src="<?=Yii::getAlias("@web")?>/images/logo_white.png" alt="L'uliveto" class="brand-image">
        </div>

        <div class="card" style="margin-top: 1rem; width: 500px; margin: 0 auto;float: none; margin-top: 20px">
            <div class="card-body table-responsive login-card-body">
            <div class="error-content" style="margin-left: auto;">
        <h3><i class="fas fa-exclamation-triangle text-danger"></i> <?= Html::encode($name) ?></h3>

        <p>
            Ops...c'è stato qualche problema nella tua richiesta.
            In questo momento non so cosa fare, per cui, intanto, potresti 
            <?= Html::a('effettuare di nuovo il login', Yii::$app->homeUrl."site/login"); ?>,
            oppure chiamare lo sviluppatore e dirgli che è successo un casino.
        </p>

    </div>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>