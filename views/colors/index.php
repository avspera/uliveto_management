<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ColorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Colori';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="color-index">

    <div class="card">
        <div class="card-header">
            <?= Html::a('Aggiungi colore', ['create'], ['class' => 'btn btn-success']) ?>
        </div>

        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'label',
                    'content',
                    'picture',
                    [
                        'class' => ActionColumn::className(),
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    


</div>
