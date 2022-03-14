<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PackagingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Confezioni';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="packaging-index">

    <div class="card">
        <div class="card-body table-responsive">

            <p><?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?></p>

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'id',
                    'name',
                    'label',
                    'image',
                    [
                        'class' => ActionColumn::className()
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
