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
            le confermiamo che l'ordine segnaposto #<?= $model->id ?> del <?= $model->formatDate($model->created_at) ?> è stato preso in carico e che gli orci saranno pronti entro il giorno concordato <br />
            Per qualsiasi richiesta rimaniamo a completa disposizione, può rispondere a questa e-mail o contattarci ai seguenti recapiti:<br />
        </p>
        <p>
            Mobili: Francesco: 3203828243 <br>
            Maria: 3807544300<br />
            Ufficio: 0828 1998201<br />
            <br />
            Saluti<br /><br />
            Grazie,<br />
            Orci del cilento
        </p>
    </div>