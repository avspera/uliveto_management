<?php 
    use yii\helpers\Html;
?>
<p>
    Ops...c'è stato qualche problema nella tua richiesta.
    In questo momento non so cosa fare, per cui, intanto, potresti 
    <?= Html::a('effettuare di nuovo il login', Yii::$app->homeUrl."site/login"); ?>,
    oppure chiamare lo sviluppatore e dirgli che è successo un casino.
</p>