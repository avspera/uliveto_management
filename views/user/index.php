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
