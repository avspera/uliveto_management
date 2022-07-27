<?php

    use yii\helpers\Html;
    use yii\helpers\Url;
    use app\models\Client;

    $today          = date("Y-m-d");
    $expiring       = app\models\Quote::find()
                        ->select(["id", "deadline", "id_client"])
                        ->andWhere(["<=", "deadline", date('Y-m-d', strtotime('+10 days'))])
                        ->andWhere(["confirmed" => 1])
                        ->orderBy(["deadline" => SORT_DESC])
                        ->limit(10)
                        ->all();
    
    $expiringCount  = app\models\Quote::find()
                                    ->where(["<=", "deadline", $today])
                                    ->andWhere(["confirmed" => 1])
                                    ->count();

    // $messageCount   = app\models\Message::find()->where(["not",  ["replied_at" => null]])->count();
    $fromWebCount   = app\models\Quote::find()->where(["from_web" => 1, 'delivered' => 0])->count();

    $fromWeb        = app\models\Quote::find()->where(["from_web" => 1, 'delivered' => 0])->all();
    $paymentsCount  = app\models\Payment::find()->count();
    $payments       = app\models\Payment::find()
                                    ->orderBy(["created_at" => SORT_DESC])
                                    ->limit(10)
                                    ->all();
?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?=\yii\helpers\Url::home()?>" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Cerca qui" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge"><?= $fromWebCount ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <?php foreach($fromWeb as $item) {
                    
                ?>
                    <a href="<?= Url::to(["quotes/view", "id" => $item->id]) ?>" class="dropdown-item">
                        <!-- Message Start -->
                        <div class="media">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    <?= $item->getClient() ?>
                                    <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm">Ha richiesto un nuovo preventivo...</p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> <?= $item->formatDate($item->created_at) ?></p>
                            </div>
                        </div>
                        <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>
                <?php } ?>
                <a href="<?= Url::to(["message/index"]) ?>" class="dropdown-item dropdown-footer">Vedi tutti</a>
            </div>
        </li>

        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-coins"></i>
                <span class="badge badge-success navbar-badge"><?= $paymentsCount ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <?php 
                        foreach($payments as $item){
                            $client = Client::find()->select(["name", "surname"])->where(["id" => $item->id_client])->one();
                    ?>
                        <div class="media">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    <?= !empty($client) ? $client->name." ".$client->surname : ""?>
                                    <span class="float-right text-sm text-<?= $item->payed ? "success" : "warning" ?>"><i class="fas fa-coins"></i></span>
                                </h3>
                                <div class="row" style="margin: 3px">
                                    <p class="text-sm" style="margin-right:5px"><?= $item->formatNumber($item->amount) ?></p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> <?= $item->formatDate($item->created_at) ?> </p>
                                </div>
                                
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                    <?php } ?>
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= Url::to(["payment/index"]) ?>" class="dropdown-item dropdown-footer">Vedi tutti</a>
            </div>
        </li>

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge"><?= $expiringCount ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header"><?= $expiringCount ?> Notifiche</span>
                <?php foreach($expiring as $item){ ?>
                    <a href="<?= Url::to(["quotes/view", "id" => $item->id])?>" class="dropdown-item">
                        <i class="fas fa-credit-card mr-2"></i> <?= $item->getClient() ?> in scadenza
                        <span class="float-right text-muted text-sm"><?= $item->formatDate($item->deadline) ?></span>
                    </a>
                    <div class="dropdown-divider"></div>
                <?php } ?>
                <a href="<?= Url::to(["quotes/index"]) ?> " class="dropdown-item dropdown-footer">Vedi tutto</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <?= Html::a('<i class="fas fa-sign-out-alt"></i>', ['/site/logout'], ['data-method' => 'post', 'class' => 'nav-link']) ?>
        </li>
    </ul>
</nav>
<!-- /.navbar -->