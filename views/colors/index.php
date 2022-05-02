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
                    [
                        'attribute' => "id_product",
                        'value' => function($model){
                            return $model->getProduct();
                        },
                        'filter' => yii\helpers\ArrayHelper::map(app\models\Product::find()->orderBy('name')->all(), 'id', 'name')
                    ],
                    [
                        'attribute' => 'picture',
                        'value' => function($model){
                            return !empty($model->picture) ? Html::img(Url::to(Yii::getAlias("@web")."/".$model->picture), ['class' => 'img-fluid img-responsive', "width" => "200px", 'alt' => $model->label, 'title' => $model->label]) : "-";
                        },
                        'format' => "raw"
                    ],
                    [
                        'class' => ActionColumn::className(),
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    


</div>
