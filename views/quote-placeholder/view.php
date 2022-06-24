<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\utils\GeneratePdf;

/* @var $this yii\web\View */
/* @var $model app\models\QuotePlaceholder */

$this->title = $model->id." - ".$model->getQuoteInfo();
$this->params['breadcrumbs'][] = ['label' => 'Preventivi Segnaposto', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>
<div class="quote-placeholder-view">

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

            <?php if(!$model->confirmed) { ?> 
                <?= Html::a('<i class="fas fa-check"></i> Conferma', ['confirm', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
            <?php } ?>
            <?= $phone ? Html::a('<i class="fas fa-comment"></i> Whatsapp', Url::to("https://wa.me/".$phone."/?text=".$text), ['class' => 'btn btn-primary', 'target' => "_blank"]) : "" ?>
            <?= Html::a('<i class="fas fa-envelope"></i> Invia email', ['generate-pdf', 'id' => $model->id, 'flag' => "send"], ['class' => 'btn btn-success']) ?>
        </div>

        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'id_quote',
                        'value' => function($model){
                            return Html::a($model->getQuoteInfo(), Url::to(["quotes/view", "id" => $model->id_quote]));
                         },
                         'format' => "raw"
                    ],
                    [
                        'attribute' => 'id_placeholder',
                        'value' => function($model){
                            return $model->getPlaceholderInfo();
                        },
                        'format' => "raw"
                     ],
                    'amount',
                    // [
                    //     'attribute' => "total_no_vat",
                    //     'value' => function($model){
                    //         return $model->getTotal();
                    //     },
                    //     'format' => "raw",
                    //     'label' => "Totale senza iva"
                    // ],
                    [
                        'attribute' => "total",
                        'value' => function($model){
                            return $model->getTotal("vat");
                        },
                        'format' => "raw",
                        'label' => "Totale"
                    ],
                    [
                        'attribute' => 'acconto',
                        'value' => function($model){
                            return $model->formatNumber($model->acconto);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'date_deposit',
                        'value' => function($model){
                            return $model->formatDate($model->date_deposit);
                        }
                    ],
                    [
                        'attribute' => 'saldo',
                        'value' => function($model){
                            return $model->formatNumber($model->saldo);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'date_balance',
                        'value' => function($model){
                            return $model->formatDate($model->date_balance);
                        }
                    ],
                    [
                        'attribute' => "confirmed",
                        'value' => function($model){
                            return $model->confirmed ? "SI" : "NO";
                        },
                        'filter' => [0 => "NO", 1 => "SI"]
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function($model){
                            return $model->formatDate($model->updated_at);
                        }
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
