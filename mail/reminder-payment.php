<?php 
    use yii\helpers\Html;
    use yii\helpers\Url;
    $paymentUrl = Yii::$app->urlManager->createAbsoluteUrl(['payment/external-payment', 'id_client' => base64_encode($client->id), "id_quote" => base64_encode($order->id), "id_payment" => base64_encode($id_payment)]);
    $paymentTarget = $payment->type ? "del saldo" : "dell'acconto";
?>
    <div class="text" style="padding: 0 3em; color: #4d4d4d">
        <p>Gentile <?= $client->name." ".$client->surname ?></p>
    </div>
    
    <div class="text" style="padding: 0 3em; color: #4d4d4d">
        <p>Grazie per aver scelto Orci Del Cilento</p>
        <p>Mancano <?= $days ?> giorni per completare il pagamento <?= $paymentTarget ?> del suo ordine #<?= $order->order_number ?> del <?= $order->formatDate($order->created_at) ?> </p>
        <p style="font-size: 20px; text-align:center"><a href="<?= $paymentUrl ?>">Clicca qui</a> per procedere al pagamento</p>
    </div>