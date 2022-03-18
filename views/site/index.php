<?php
    use yii\helpers\Url;
    $this->title = 'Home';
    $this->params['breadcrumbs'] = [['label' => $this->title]];
?>
<div class="container-fluid">

    <div class="row">
        <div class="col-md-6 col-sm-6 col-12">
            <a href= "<?= Url::to(["quotes/create"]) ?>">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Nuovo preventivo</span>
                    </div>
                </div>
            </a>
        </div>

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

    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="small-box bg-success">
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
    </div>

    <div class="row">
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
    </div>

</div>