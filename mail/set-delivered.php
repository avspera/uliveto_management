
    <div class="text" style="padding: 0 3em; color: #4d4d4d">
        <p>Gentile <?= $model->getClient() ?></p>
    </div>
    
    <div class="text" style="padding: 0 3em; color: #4d4d4d">
        <p>Le confermiamo che l'ordine #<?= isset($model->order_number) ? $model->order_number : $model->id ?> del <?= $model->formatDate($model->created_at) ?> è stato consegnato con successo</p>
        <p>Grazie per aver scelto Orci Del Cilento.</p>
    </div>