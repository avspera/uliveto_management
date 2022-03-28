<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;

$this->title = 'Clienti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

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

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="card">
        <div class="card-header"><?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?></div>
        <div class="card-body table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'responsive' => true,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'name',
                    'surname',
                    'email:email',
                    'phone',
                    'age',
                    [
                       'attribute' => 'occurrence',
                        'value' => function($model){
                            return $model->getOccurrence();
                        },
                        'filter' => $searchModel->getOccurrenceList()
                    ],
                    [
                        'class' => ActionColumn::className(),
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
