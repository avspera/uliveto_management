<?php 
/**
 * example template body
 * 
 * 
 */
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
    <div class="text" style="padding: 0 3em; color: #4d4d4d">
        <p>Gentile <?= $client->name." ".$client->surname ?></p>
    </div>
    <div class="text" style="padding: 0 3em; color: #4d4d4d">
        <p>
            Le confermiamo che il pagamento per l'ordine # <?= $order->order_number?> è stato completato con successo. <br />
            Grazie per aver scelto Orci del Cilento.
        </p>
        <p>
            Mobili:<br> 
            Francesco: 3203828243 <br />
            Maria: 3807544300<br />
            Ufficio: 0828 1998201<br />
            <br />
            Saluti<br /><br />
            Grazie,<br />
            Orci del cilento
        </p>
    </div>