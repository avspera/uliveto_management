<?php 
    use yii\helpers\Html;
    $this->title = "Aggiungi preventivo";
?>

<div class="card card-info">
    <div class="card-header"><div class="text-lg">Scegli</div></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-6 justify-content-center" >
                <?= Html::a('<i class="fas fa-plus"></i> Bomboniere', ['create'], ['class' => 'btn btn-lg btn-success']) ?>
            </div>
            <div class="col-md-6 col-sm-6 col-6 justify-content-center">
                <?= Html::a('<i class="fas fa-plus"></i> Segnaposto', ['quote-placeholder/create'], ['class' => 'btn btn-lg btn-success']) ?>
            </div>
        </div>
    </div>
</div>