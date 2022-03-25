<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\InviaCatalogo */

$this->title = "Lista cataloghi caricati";
$this->params['breadcrumbs'][] = ['label' => 'Invia Catalogo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="invia-catalogo-view-catalogs">

    
        <div class="card">
            <div class="card-body">
                <div class="col-md-12 table-responsive">
                    <table id="myTable" class="table table-bordered table-hover">
                        <thead>
                            <th class="sorting sorting_asc" tabindex="0" aria-controls="myTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Nome file">Nome file</th>
                            <th class="sorting sorting_asc" tabindex="0" aria-controls="myTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Creato il">Creato il</th>
                            <th class="sorting sorting_asc" tabindex="0" aria-controls="myTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Scarica">Scarica</th>
                            <th class="sorting sorting_asc" tabindex="0" aria-controls="myTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Cancella">Cancella</th>
                        </thead>
                        <tbody>
                            <?php
                                $i = 0; 
                                foreach($existingFiles as $file) { 
                                    $filename =  substr($file, strrpos($file, "/")+1);
                            ?> 
                                <tr id="<?= $i ?>" class="<?= $i%2 ? "event" : "odd" ?> ">
                                    <td class="dtr-control sorting_1" tabindex="<?= $i ?>"><?= $filename  ?></td>
                                    <td class="dtr-control sorting_1" tabindex="<?= $i ?>"><?= date ("d/m/y H:i.", filemtime($file)) ?></td>
                                    <td style='text-align:center' class="dtr-control sorting_1" tabindex="<?= $i ?>"><?= Html::a('<i class="fas fa-file-pdf"></i>', Url::to(["/pdf/".$filename]), ['class' => 'btn btn-success', 'target' => "_blank"]) ?></td>
                                    <td style='text-align:center' class="dtr-control sorting_1" tabindex="<?= $i ?>"><?= Html::a('<i class="fas fa-trash"></i>', Url::to(["delete-catalog", "name" => $file]), ['class' => 'btn btn-danger']) ?></td>
                                </tr>
                            <?php $i++; }?> 
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
</div>


<script>

</script>