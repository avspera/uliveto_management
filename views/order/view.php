<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
$client = $model->getClient();
$clientPhone = $model->getClientPhone();
$deadline = $model->formatDate($model->deadline);
$this->title = $model->order_number." - ".$client;
$this->params['breadcrumbs'][] = ['label' => 'Ordini', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$encodedText = 
    "Ho il piacere di presentarle la nostra collezione di orci realizzati e decorati a mano.L'orcio in ceramica contiene e custodisce i profumi e i sapori dell'olio extravergine di oliva biologico, prodotto nei nostri uliveti a *Trentinara e Giungano*<br /><br />Scadenza offerta: ".$model->formatDate($model->deadline)."<br /><br />Rimango a sua completa disposizione<br />Cordiali Saluti<br /><br />Francesco Guariglia<br />Mobile: 39 3203828243<br />Maria Guariglia <br />mobile: 39 3807544300<br />mail: e-commerce@ulivetodimaria.it"
;
$decodedText = str_ireplace("<br />", "\r\n", $encodedText);
$text   = urlencode($decodedText);
$phone  = $clientPhone ? "0039".trim($clientPhone) : 0;

?>

<div class="order-view">

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
            <?= Html::a('<i class="fas fa-file-pdf"></i> Genera PDF', ['order/generate-pdf', 'id' => $model->id, 'flag' => "generate"], ['class' => 'btn btn-success']) ?>
            <?= $phone ? Html::a('<i class="fas fa-comment"></i> Whatsapp', Url::to("https://wa.me/".$phone."/?text=".$text), ['class' => 'btn btn-primary', 'target' => "_blank"]) : "" ?>
            <?= Html::a('<span style="color:white"><i class="fas fa-upload"></i> Carica allegati</span>', ['upload-files', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <?= !$model->delivered ? Html::a('<span style="color:white"><i class="fas fa-truck"></i> CONSEGNATO</span>', ['set-delivered', 'id' => $model->id], ['class' => 'btn btn-success']) : "" ?>
        </div>

        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'order_number',
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return $model->formatDate($model->created_at, true);
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function($model){
                            return $model->formatDate($model->updated_at, true);
                        }
                    ],
                    [
                        'attribute' => 'id_client',
                        'value' => function($model){
                            return Html::a($model->getClient(), Url::to(["clients/view", "id" => $model->id_client]));
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'placeholder',
                        'value' => function($model){
                            return $model->getPlaceholder();
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => "confetti",
                        'value' => function($model){
                            $html = "";
                            if($model->confetti){
                                if($model->confetti_omaggio){
                                    $html .= "<span style='text-decoration: line-through;'>".$model->formatNumber($model->prezzo_confetti)."</span> <span style='color: green'> - in omaggio</span>";
                                }
                                else{
                                    $html .= "<span>".$model->formatNumber($model->prezzo_confetti)."</span>";
                                }
                            }else{
                                $html .= "-";
                            }
                            return $html;
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => "custom_amount",
                        'value' => function($model){
                            $html = "";
                            if($model->custom_amount){
                                if($model->custom_amount_omaggio){
                                    $html .= "<span style='text-decoration: line-through;'>".$model->formatNumber($model->custom_amount)."</span> <span style='color: green'> - in omaggio</span>";
                                }
                                else{
                                    $html .= "<span>".$model->formatNumber($model->custom_amount)."</span>";
                                }
                            }else{
                                $html .= "-";
                            }
                            return $html;
                        },
                        'format' => "raw"
                    ],
                    'custom:ntext',
                    'notes:ntext',
                    [
                        'attribute' => 'total_no_vat',
                        'value' => function($model){
                            return $model->formatNumber($model->total_no_vat);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'total',
                        'value' => function($model){
                            return $model->formatNumber($model->total);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'id_sconto',
                        'value' => function($model){
                            return $model->getSale($model->id_sconto);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'deposit',
                        'value' => function($model){
                            return $model->formatNumber($model->deposit);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'balance',
                        'value' => function($model){
                            $saldo = $model->total - $model->deposit;
                            return $model->formatNumber($saldo);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'shipping',
                        'value' => function($model){
                            return $model->shipping ? $model->address : "NO";
                        }
                    ],
                    [
                        'attribute' => 'data_evento',
                        'value' => function($model){
                            return $model->formatDate($model->data_evento);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'deadline',
                        'value' => function($model){
                            return $model->formatDate($model->deadline);
                        },
                        'format' => "raw"
                    ],
                    
                    [
                        'attribute' => "confirmed",
                        "value" => function($model){
                            return $model->confirmed ? "SI" : "NO";
                        }
                    ],
                    [
                        'attribute' => 'delivered',
                        'value' => function($model){
                            return $model->delivered ? "SI" : "NO";
                        },
                    ],
                    [
                        'attribute' => "attachments",
                        'value' => function($model){
                            if(!empty($model->attachments)){
                                $html = "<div class='row'>";
                                $attachments = json_decode($model->attachments, true);
                                foreach($attachments as $file){
                                    $html .= "<div style='margin: 5px'>";
                                    $html .= Html::a("<i class='fas fa-file'></i> Allegato", Url::to([$file]));
                                    $html .= "</div>";
                                }
                                $html .= "</div>";

                                return $html;
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
    
    <div class="card card-secondary">
        <div class="card-header"><div class="text-md">Totale servizi aggiuntivi</div></div>
        <div class="card-body">
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute' => 'total_segnaposto',
                            'value' => function($model){
                                return $model->getPlaceholderTotal();
                            },
                            'format' => "raw",
                            'label' => "Totale Segnaposto"
                        ],
                        [
                            'attribute' => 'total_confetti',
                            'value' => function($model){
                                return $model->confetti_omaggio ? "In omaggio" : $model->formatNumber($model->getTotalAmount()*$model->prezzo_confetti);
                            },
                            'format' => "raw",
                            'label' => "Totale Confetti"
                        ],
                    ]
            ]) ?>
        </div>
    </div>
         
    <div class="card card-info">
        <div class="card-header">
            <div class="text-md">Dettagli prodotti</div>
        </div>

        <div class="card-body">
    
            <?= GridView::widget([
                'dataProvider'  => $products,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'id_product',
                        'value' => function($model){
                            return $model->getProduct();
                        },
                    ],
                    'amount',
                    [
                        'attribute' => 'id_color',
                        'value' => function($model){
                            return !empty($model->id_color) ? $model->getColor() : "";
                        },
                    ],
                    [
                        'attribute' => "custom_color",
                    ],
                    [
                        'attribute' => 'id_packaging',
                        'value' => function($model){
                            return $model->getPackaging();
                        },
                    ],
                ]
            ]); ?>
        </div>
        
    </div>
        
    <?php if(!empty($segnaposto)) { ?>
        <div class="card card-info">
                <div class="card-header">
                    <div class="text-md">Segnaposto 
                        <?= Html::a('<i class="fas fa-plus-circle" style="margin-left: 5px;"></i>', ['quote-placeholder/create', 'id_quote' => $model->id]) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?= GridView::widget([
                        'dataProvider'  => $segnaposto,
                        'filterModel'   => $quotePlaceholderModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                            'attribute' => 'id_quote',
                            'value' => function($model){
                                return Html::a($model->getQuoteInfo(), Url::to(["quote/view", "id" => $model->id]));
                                },
                                'format' => "raw"
                            ],
                            [
                                'attribute' => 'id_placeholder',
                                'value' => function($model){
                                    return $model->getPlaceholderInfo();
                                },
                                'format' => "raw",
                                'filter' => yii\helpers\ArrayHelper::map(app\models\Segnaposto::find()->orderBy('label')->all(), 'id', 'label')
                            ],
                            'amount',
                            [
                                'attribute' => "total_no_vat",
                                'value' => function($model){
                                    return $model->getTotal();
                                },
                                'format' => "raw",
                                'label' => "Totale senza iva"
                            ],
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
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'view' => function ($model) {
                                        return Html::a(
                                            '<span class="fas fa-eye"></span>',
                                            Url::to(["/quote-placeholder/view", "id" => $model->id])
                                            
                                        );
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    <?php } ?> 

    <?php if(!empty($payments)) { ?>
        <div class="card card-info">
                <div class="card-header">
                    <div class="text-md">Pagamenti bomboniere
                        <?= Html::a('<i class="fas fa-plus-circle" style="margin-left: 5px;"></i>', ['payment/create']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?= GridView::widget([
                        'dataProvider' => $payments,
                        'columns' => [
                            [
                                'attribute' => "type",
                                'value' => function($model){
                                    return $model->getType();
                                }
                            ],
                            [
                                'attribute' => 'amount',
                                'value' => function($model){
                                    return $model->formatNumber($model->amount);
                                },
                                'format' => "raw"
                            ],
                            [
                                'attribute' => 'created_at',
                                'value' => function($model){
                                    return $model->formatDate($model->created_at);
                                },
                                'label' => "Data pagamento"
                            ],
                            [
                                'attribute' => "payed",
                                'value' => function($model){
                                    return $model->isPayed();
                                },
                                'format' => "raw",
                                'filter' => [0 => "NO", 1 => "SI"]
                            ],
                            [
                                'attribute' => "fatturato",
                                'value' => function($model){
                                    return $model->isFatturato();
                                },
                                'format' => "raw",
                                'filter' => [0 => "NO", 1 => "SI"]
                            ],
                            [
                                'attribute' => "saldo",
                                'value' => function($model){
                                    $totale = $model->getTotal();
                                    if(!$totale) return;
                                    return $totale - $model->amount < 0 ? 0 : $model->formatNumber($totale - $model->amount);
                                },
                                'format' => "raw",
                                'label' => "Resta da saldare"
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {update} {delete}',
                                'buttons'=> [
                                    'view' => function ($url, $model) {
                                        return Html::a("<i class='fas fa-eye'></i>", ["payment/view", "id" => $model->id]);
                                    },
                                    'update' => function ($url, $model) {
                                        return Html::a("<i class='fas fa-pencil-alt'></i>", ["payment/update", "id" => $model->id]);
                                    },
                                    'delete' => function ($url, $model) {
                                        return Html::a("<i class='fas fa-trash'></i>", ["payment/delete", "id" => $model->id], [
                                            'title' => "Activate",
                                            'data' => [
                                                 'method' => 'post',
                                                 'confirm' => 'Sei sicuro di voler cancellare questo elemento?',
                                            ],
                                        ]);
                                    }
                                ],
                            ]
                        ],
                    ]); ?>
                </div>
        </div>
    <?php } ?>

    <?php if(!empty($paymentsPlaceholder)) { ?>
        <div class="card card-info">
                <div class="card-header">
                    <div class="text-md">Pagamenti segnaposto
                        <?= Html::a('<i class="fas fa-plus-circle" style="margin-left: 5px;"></i>', ['payment/create']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?= GridView::widget([
                        'dataProvider' => $paymentsPlaceholder,
                        'columns' => [
                            [
                                'attribute' => "type",
                                'value' => function($model){
                                    return $model->getType();
                                }
                            ],
                            [
                                'attribute' => 'amount',
                                'value' => function($model){
                                    return $model->formatNumber($model->amount);
                                },
                                'format' => "raw"
                            ],
                            [
                                'attribute' => 'created_at',
                                'value' => function($model){
                                    return $model->formatDate($model->created_at);
                                },
                                'label' => "Data pagamento"
                            ],
                            [
                                'attribute' => "payed",
                                'value' => function($model){
                                    return $model->isPayed();
                                },
                                'format' => "raw",
                                'filter' => [0 => "NO", 1 => "SI"]
                            ],
                            [
                                'attribute' => "fatturato",
                                'value' => function($model){
                                    return $model->isFatturato();
                                },
                                'format' => "raw",
                                'filter' => [0 => "NO", 1 => "SI"]
                            ],
                            [
                                'attribute' => "saldo",
                                'value' => function($model){
                                    $pagamenti = $model->checkPayments();
                                    
                                    if($pagamenti == 2){
                                        return 0;
                                    }else if ($pagamenti == 1){
                                        $saldo = $model->getSaldo();
                                        return $model->formatNumber($saldo);
                                    }else{
                                        $totale = $model->getTotal();
                                        return $model->formatNumber($totale);
                                    }
                                },
                                'format' => "raw",
                                'label' => "Resta da saldare"
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {update} {delete}',
                                'buttons'=> [
                                    'view' => function ($url, $model) {
                                        return Html::a("<i class='fas fa-eye'></i>", ["payment/view", "id" => $model->id]);
                                    },
                                    'update' => function ($url, $model) {
                                        return Html::a("<i class='fas fa-pencil-alt'></i>", ["payment/update", "id" => $model->id]);
                                    },
                                    'delete' => function ($url, $model) {
                                        return Html::a("<i class='fas fa-trash'></i>", ["payment/delete", "id" => $model->id], [
                                            'title' => "Activate",
                                            'data' => [
                                                 'method' => 'post',
                                                 'confirm' => 'Sei sicuro di voler cancellare questo elemento?',
                                            ],
                                        ]);
                                    }
                                ],
                            ]
                        ],
                    ]); ?>
                </div>
        </div>
    <?php } ?>
</div>