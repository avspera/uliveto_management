<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clienti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <p><?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="card">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'id',
                    'name',
                    'surname',
                    'email:email',
                    'phone',
                    //'age',
                    [
                       'attribute' => 'occurrence',
                        'value' => function($model){
                            return $model->getOccorrence();
                        }
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'urlCreator' => function ($action, Client $model, $key, $index, $column) {
                            return Url::toRoute([$action, 'id' => $model->id]);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn', 'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => function($url, $model, $key) {
                                $url = Url::to(["view", 'id' => $model->id]);
                                $btn = Html::a("<i class='fa fa-eye'></i>", $url);
                                return $btn;
                            },
                            'update' => function($url, $model, $key) {
                                $url = Url::to(["update", 'id' => $model->id]);
                                $btn = Html::a("<i class='fa fa-pencil'></i>", $url);
                                return $btn;
                            },
                            'delete' => function($url, $model) {
                                //isadmin
                                // if(Yii::$app->user->identity->isGestore() == 0)
                                    return Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
                                                'class' => '',
                                                'data' => [
                                                    'confirm' => 'Sei sicuro di voler eliminare questo servizio?',
                                                    'method' => 'post',
                                                ],
                                    ]);
                                // else 
                                //     return "";
                            }
                        ],
                    ],  
                ],
            ]); ?>
        </div>
    </div>

</div>
