<?php 
    use yii\helpers\Url;
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= Url::to(["site/index"]) ?>" class="navbar-brand brand-link">
        <img class="img-responsive" src="<?=Yii::getAlias("@web")."/images/logo_white.png"?>" alt="Azienda Agricola L'Uliveto">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?=$assetDir?>/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a class="d-block" href="<?= Url::to(["user/view", "id" => Yii::$app->user->identity->id]) ?>"><?= isset(Yii::$app->user->identity->username) ? Yii::$app->user->identity->username : "Unlogged user" ?></a>
            </div>
                
        </div>

        

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?php
            echo \hail812\adminlte\widgets\Menu::widget([
                'items' => [
                    ['label' => 'ORCI', 'header' => true],
                    ['label' => 'Preventivi', 'url' => ['quotes/index'], 'icon' => 'file-alt', 'visible' => (Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 1)],
                    ['label' => 'Ordini', 'url' => ['order/index'], 'icon' => 'credit-card', 'visible' => Yii::$app->user->identity->role == 0],
                    ['label' => 'Clienti',  'icon' => 'users', 'url' => ['/clients/index'], 'target' => '_self', 'visible' => (Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 2)],
                    ['label' => 'Pagamenti',  'icon' => 'coins', 'url' => ['/payment/index'], 'target' => '_self', 'visible' => (Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 3)],
                    ['label' => 'Catalogo',  'icon' => 'file', 'url' => ['/invia-catalogo/index'], 'target' => '_self', 'visible' => (Yii::$app->user->identity->role == 0)],
                    ['label' => 'TOOLS', 'header' => true, 'visible' => Yii::$app->user->identity->role == 0],
                    [
                        'label' => 'Prodotti',
                        'iconStyle' => 'far',
                        'items' => [
                            ['label' => 'Nuovo', 'url' => ["product/create"], 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                            ['label' => 'Lista', 'url' => ["product/index"], 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                        ],
                        'visible' => Yii::$app->user->identity->role == 0
                    ],
                    [
                        'label' => 'Occorrenze',
                        'iconStyle' => 'far',
                        'items' => [
                            ['label' => 'Nuovo', 'url' => ["occurrence/create"], 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                            ['label' => 'Lista', 'url' => ["occurrence/index"], 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                        ],
                        'visible' => Yii::$app->user->identity->role == 0
                    ],
                    [
                        'label' => 'Colori',
                        'iconStyle' => 'far',
                        'items' => [
                            ['label' => 'Nuovo', 'url' => ["colors/create"], 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                            ['label' => 'Lista', 'url' => ["colors/index"],'iconStyle' => 'far', 'icon' => 'dot-circle'],
                        ],
                        'visible' => Yii::$app->user->identity->role == 0
                    ],
                    [
                        'label' => 'Confezioni',
                        'iconStyle' => 'far',
                        'items' => [
                            ['label' => 'Nuovo', 'url' => ["packagings/create"], 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                            ['label' => 'Lista', 'url' => ["packagings/index"], 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                        ],
                        'visible' => Yii::$app->user->identity->role == 0
                    ],
                    [
                        'label' => 'Sconti',
                        'iconStyle' => 'far',
                        'items' => [
                            ['label' => 'Nuovo', 'url' => ["sales/create"], 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                            ['label' => 'Lista', 'url' => ["sales/index"], 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                        ],
                        'visible' => Yii::$app->user->identity->role == 0
                    ],
                    ['label' => 'PEOPLE', 'header' => true, 'visible' => Yii::$app->user->identity->role == 0],
                    ['label' => 'Utenti',  'icon' => 'users', 'url' => ['/user/index'], 'target' => '_self', Yii::$app->user->identity->isAdmin(), 'visible' => Yii::$app->user->identity->role == 0],
                    ['label' => 'Esci', 'url' => ['site/logout'], 'icon' => 'sign-out-alt', 'visible' => !Yii::$app->user->isGuest]
                ],
            ]);
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>