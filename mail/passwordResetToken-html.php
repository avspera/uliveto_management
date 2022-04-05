<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Ciao <?= Html::encode($user->username) ?>,</p>

    <p>Clicca su questo link per reipostare la password.</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>

    <p>Se non sei stato tu a richiedere questa procedura, ignora questa email</p>
    <small>
        Questa email è inviata automaticamente dal sistema 'OrciDelCilento' Per piacere, non rispondere a questa email.
        <br>
        Se hai bisogno di assistenza, scrivi a speradeveloper@gmail.com
    </small>
</div>
