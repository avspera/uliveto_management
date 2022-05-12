<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = $model->id." - ".$model->getClient();
$this->params['breadcrumbs'][] = ['label' => 'Pagamenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$id_quote = !empty($model->id_quote) ? $model->id_quote : $model->id_quote_placeholder;

?>
<div class="paymeny-view">

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
            <?= Html::a('<i class="fas fa-file-pdf"></i> Genera fattura pro forma', ['generate-fattura-pro-forma', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-file-pdf"></i> Segna come fatturato', ['set-as-invoiced', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
            <?= Html::a('<span style="color:white"><i class="fas fa-envelope"></i> Invia email pagamento</span>', ['send-email-payment', 'id_client' => $model->id_client, "id_quote" => $id_quote, "id_payment" => $model->id], ['class' => 'btn btn-success']) ?>
        </div>

        <div class="card-body table-responsive">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'id_client',
                        'value' => function($model){
                            return $model->getClient();
                        }
                    ],
                    [
                        'attribute' => 'id_quote',
                        'value' => function($model){
                            $quote = $model->getQuote();
                            return Html::a($quote["quote"], Url::to([$quote["confirmed"] ? "order/view" : "quotes/view", "id" => $model->id_quote]));
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'id_quote_placeholder',
                        'value' => function($model){
                            $quote = $model->getQuotePlaceholder();
                            return $model->id_quote_placeholder !== NULL ? Html::a($quote["quote"], Url::to(["quote-placeholder/view", "id" => $model->id_quote_placeholder])) : "-";
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'amount',
                        'value' => function($model){
                            return $model->formatNumber($model->amount);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => "type",
                        'value' => function($model){
                            return $model->getType();
                        }
                    ],
                    [
                        'attribute' => "fatturato",
                        'value' => function($model){
                            return $model->isFatturato();
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at);
                        }
                    ],
                    [
                        'attribute' => "allegato",
                        'value' => function($model){
                            if(!empty($model->allegato)){
                                return "<a target='_blank' href='".Url::to([$model->allegato])."'>Ricevuta di pagamento</a>";
                            }else{
                                return "-";
                            }
                        },
                        'format' => "raw"
                    ]
                ],
            ]) ?>
        </div>
    </div>
    
</div>
