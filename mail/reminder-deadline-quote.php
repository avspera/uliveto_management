<?php 
    use yii\helpers\Html;
    use yii\helpers\Url;
    $paymentUrl = Yii::$app->urlManager->createAbsoluteUrl(['payment/external-payment', 'id_client' => base64_encode($client->id), "id_quote" => base64_encode($order->id), "id_payment" => base64_encode($id_payment)]);
?>
<style>
    .text{
        font-size: 14px;
    }
</style>

    <div class="text" style="padding: 0 3em; color: #4d4d4d">
        <p>Gentile <?= $client->name." ".$client->surname ?></p>
    </div>
    
    <div class="text" style="padding: 0 3em; color: #4d4d4d">
        <p>Grazie per aver scelto Orci Del Cilento</p>
        <p>Ti ricordiamo che mancano <?= $days ?> giorni alla scadenza dell'offerta per il tuo preventivo #<?= $oder->order_number ?> del <?= $oder->formatDate($order->created_at) ?> </p>
    </div>