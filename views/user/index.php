<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Utenti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

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
            <?= Html::a('Aggiungi utente', ['create'], ['class' => 'btn btn-success']) ?>
        </div>

        <div class="card-body table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'username',
                    'email:email',
                    [
                       'attribute' => 'status',
                       'value' => function($model){
                           return $model->getStatus();
                       },
                       'filter' => $searchModel->statusList
                    ],
                    [
                        'attribute' => 'role',
                        'value' => function($model){
                            return $model->getRole();
                        },
                        'filter' => $searchModel->roleList
                     ],
                    [
                        'attribute' => 'created',
                        'value' => function($model){
                            return $model->formatDate($model->created);
                        }
                     ],
                    'updated',
                    [
                        'class' => ActionColumn::className(),
                        
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

</div>
