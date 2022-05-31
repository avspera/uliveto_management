<?php
    use yii\helpers\Url;
    use dosamigos\chartjs\ChartJs;
 
    $this->title = 'Home::OrciDelCilento - Manager';
    $this->params['breadcrumbs'] = [['label' => $this->title]];
?>
<div class="container-fluid">

    <div class="row">
        <?php if(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 1) { ?>
        <div class="col-md-6 col-sm-6 col-12">
            <a href= "<?= Url::to(["quotes/choose-quote"]) ?>">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Nuovo preventivo</span>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>
        
        <?php if(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 2) { ?>
        <div class="col-md-6 col-sm-6 col-12">
            <a href= "<?= Url::to(["clients/create"]) ?>">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-user-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Nuovo cliente</span>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>
    </div>

    <div class="row">
        <?php if(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 1) { ?>

            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3><?= $quotesCount ?></h3>
                        <p>Nuovi Preventivi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="<?= Url::to(["quotes/index"]) ?>" class="small-box-footer">
                        Tutti <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        <?php } ?>

        <?php if(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 1) { ?>
            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $ordersCount ?></h3>
                        <p>Ordini confermati</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="<?= Url::to(["order/index"]) ?>" class="small-box-footer">
                        Tutti <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="row">
        <?php if(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 2) { ?>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $clientsCount ?></h3>
                        <p>Clienti</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="<?= Url::to(["clients/index"]) ?>" class="small-box-footer">
                        Tutti <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        <?php } ?>

        <?php if(Yii::$app->user->identity->role == 0) { ?>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3><?= $messagesCount ?></h3>
                        <p>Messaggi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="<?= Url::to(["message/index"]) ?>" class="small-box-footer">
                        Tutti <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= ChartJs::widget([
                    'type' => 'bar',
                    'options' => [
                        'height' => 300,
                        'width' => 500
                    ],
                    'data' => [
                        'labels' => ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"],
                        'datasets' => [
                            [
                                'label' => "Preventivi",
                                'backgroundColor'       => ["orange",],
                                'borderColor'           => "rgba(179,181,198,1)",
                                'pointBorderColor'      => "#fff",
                                'pointHoverBackgroundColor' => "#fff",
                                'pointHoverBorderColor' => "rgba(179,181,198,1)",
                                'data' => [$quotesCount]
                            ],
                            [
                                'label' => "Ordini",
                                'backgroundColor'       => ["green"],
                                'borderColor'           => "rgba(179,181,198,1)",
                                'pointBorderColor'      => "#fff",
                                'pointHoverBackgroundColor' => "#fff",
                                'pointHoverBorderColor' => "rgba(179,181,198,1)",
                                'data' => [$ordersCount]
                            ],
                        ]
                    ]
                ]);
            ?>
        </div>
    </div>
</div>