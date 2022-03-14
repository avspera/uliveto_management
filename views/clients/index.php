<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
// use yii\grid\GridView;
use kartik\grid\GridView;
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
        <div class="card-body table-responsivge table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
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
