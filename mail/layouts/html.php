<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this view component instance */
/** @var \yii\mail\MessageInterface $message the message being composed */
/** @var string $content main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        .text{
            font-family: 'Montserrat', medium;
        }

        body{
            font-size: 1.4em;
        }
        
    </style>
</head>
<body>
    <?php $this->beginBody() ?>

    <center>

         <div style="max-width: 600px; margin: 0 auto;" class="email-container">
                <!-- BEGIN BODY -->
            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                <tr>
                <td valign="top" class="bg_white" style="padding: 1em 2.5em;">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td class="logo" style="text-align: center; ">
                                <h1><a href="#"><?= Html::img(Url::to("https://manager.orcidelcilento.it/web/images/logo_mail.png"), ["style" => "width: 300px"]) ?></a></h1>
                            </td>
                        </tr>
                    </table>
                </td>
                </tr><!-- end tr -->
                        <tr>
                        <td valign="middle" class="hero hero-2 bg_white" style="padding: 4em 0;">
                            <table  align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                                <tr>
                                    <td>
                                        <?= $content ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                </tr><!-- end tr -->
            </table>
        </div>
            
         <!--footer -->
        <a href="#"><?= Html::img(Url::to("https://manager.orcidelcilento.it/web/images/ok_lettering_payoff.png"), ["style" => "width: 300px"]) ?></a>

        <div style="width: fit-content; block-size: fit-content; margin-top:-40px">
            <?= Html::img(Url::to("https://manager.orcidelcilento.it/web/images/ok_216_footer_newsletter.png"), ["style" =>  "width: 100%"]) ?>
        </div>

        <div style="width: fit-content; block-size: fit-content;color: #4c552b">
            <div class="text center"><b>Azienda Agricola l'Uliveto di Guariglia Maria</b></div>
            <div class="text center">
                    Via Europa, 57 - 84070 - Trentinara (SA) - ltaly | mobile: 320 382 8243 I 380 754 4300 I fisso: +39 0828 199 8201 <br />
                    P. IVA 05564280658 I Pec: aziendauliveto@pec.it | E-mail: info@orcidelcilento<br />
                    aziendaagricolaluliveto@gmail.com<br />
                    <a href="www.orcidelcilento.it">www.orcidelcilento.it</a> - <a href="www.orcidelcilento.it">www.ulivetodimaria.it</a>
            </div>
        </div>

        <div style="width: fit-content; block-size: fit-content; color: black">
            <div class="text center color: #4c552b">Seguici anche su</div>
            <div class="text center">
                <a href="https://www.facebook.com/ulivetotrentinara"><?= Html::img(Url::to("https://manager.orcidelcilento.it/web/images/ok_fb_icon_gray.png"), ["style" => "width: 30px; margin: 5px"]) ?></a>
                <a href="https://www.instagram.com/aziendaluliveto"><?= Html::img(Url::to("https://manager.orcidelcilento.it/web/images/ok_insta_icon_gray.png"), ["style" => "width: 30px; margin: 5px"]) ?></a>
            </div>
        </div>
        <!--footer -->


    </center>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
