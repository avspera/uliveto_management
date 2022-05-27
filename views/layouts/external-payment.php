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
    <title>Riepilogo pagamento - OrciCilento</title>
    <?php $this->head() ?>
    <script src="https://www.paypal.com/sdk/js?client-id=AYww-a_619Zh_DtvXuE1S_UjJFfdj9yfEqlxUK7f9BrO9JTHnLImhBgt2osjCCg27QB2rOwjs0ar0GuN&currency=EUR"></script>
</head>
<body id="page-top" class="bg-gradient-success">
<?php $this->beginBody() ?>

    <div class="container" style="margin-top: 20px">
        <div class="d-flex justify-content-center">
            <img src="<?=Yii::getAlias("@web")?>/images/logo_white.png" alt="L'uliveto" class="brand-image">
        </div>

        <?= $content ?>

    </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>