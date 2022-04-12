<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\InviaCatalogo */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Invia Catalogo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$encodedText = 
    "Ciao, ".$model->name."<br />Ho il piacere di presentarle la nostra collezione di orci realizzati e decorati a mano.L'orcio in ceramica contiene e custodisce i profumi e i sapori dell'olio extravergine di oliva biologico, prodotto nei nostri uliveti a *Trentinara e Giungano*<br /><br />Rimango a sua completa disposizione<br />Cordiali Saluti<br /><br />Francesco Guariglia<br />Mobile: 39 3203828243<br />Maria Guariglia <br />mobile: 39 3807544300<br />mail: e-commerce@ulivetodimaria.it"
;

$decodedText = str_ireplace("<br />", "\r\n", $encodedText);
$text   = urlencode($decodedText);
$phone  = $model->telefono ? "0039".trim($model->telefono) : 0;

?>
<div class="invia-catalogo-view">

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
        <div class="card-header">
            <?= Html::a('<i class="fas fa-pencil-alt"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Cancella', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Sei sicuro di voler cancellare questo elemento?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('<i class="fas fa-envelope"></i> Email', ['send-catalog', 'id' => $model->id, "flag" => "email"], ['class' => 'btn btn-success']) ?>
            <?= $phone ? Html::a('<i class="fas fa-comment"></i> Whatsapp', Url::to("https://wa.me/".$phone."/?text=".$text), ['class' => 'btn btn-primary', 'target' => "_blank"]) : "" ?>
        </div>
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'email:email',
                    'name',
                    'telefono',
                ],
            ]) ?>
        </div>
    </div>
    
</div>
