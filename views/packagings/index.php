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
        <div class="card-body table-responsive">

            <p><?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?></p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'name',
                    'label',
                    [
                       'attribute' => 'price',
                       'value' => function($model){
                           return $model->formatNumber($model->price);
                       },
                       'format' => "raw"
                    ],
                    [
                        'attribute' => 'image',
                        'value' => function($model){
                            return !empty($model->image) ? Html::img(Url::to($model->image), ['class' => "img-responsive"]) : "-";
                        }
                    ],
                    [
                        'class' => ActionColumn::className()
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
