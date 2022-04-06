<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Quote */

$this->title = 'Modifica Ordine: ' . $model->id." - ".$model->getClient();
$this->params['breadcrumbs'][] = ['label' => 'Ordini', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="quote-update">
    <?php if(Yii::$app->session->hasFlash('error')): ?>
      <div class="alert alert-warning alert-dismissible" style="color: white">
        <?php echo Yii::$app->session->getFlash('error'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?> 

    <?php if(Yii::$app->session->hasFlash('success')): ?>
      <div class="alert alert-success alert-dismissible">
        <?php echo Yii::$app->session->getFlash('success'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?> 
    <div class="card">
        <div class="card-body table-responsive">
            <?= $this->render('_form', [
                'model'         => $model, 
                'segnaposto'    => $segnaposto,
                'quoteDetails'  => $quoteDetails,
                'products'      => $products,
                'detailsModel'  => $detailsModel,
                'colors'        => $colors,
                'packagings'    => $packagings
            ]) ?>
        </div>
    </div>
</div>
