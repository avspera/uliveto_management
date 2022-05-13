<?php 
/**
 * example template body
 * 
 * 
 */
    use yii\helpers\Html;
    use yii\helpers\Url;
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
        <p>
            Grazie di aver scelto Orci del Cilento<br />
            Qui di seguito troverà il riepilogo ordine e il link per effettuare il relativo pagamento.
        </p>
        <p>
            Mobili: Francesco: 3203828243 <br />
            Maria: 3807544300<br />
            Ufficio: 0828 1998201<br />
            <br />
            Saluti<br /><br />
            Grazie,<br />
            Orci del cilento
        </p>
    </div>