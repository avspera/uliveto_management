<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OccurrenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Occorrenze';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="occurrence-index">

    <div class="card">
        <div class="card-body">
            <p>
                <?= Html::a('Aggiungi', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'label',
                    // [
                    //     'class' => ActionColumn::className(),
                    //     'urlCreator' => function ($action, Occurrence $model, $key, $index, $column) {
                    //         return Url::toRoute([$action, 'id' => $model->id]);
                    //     }
                    // ],
                ],
            ]); ?>
        </div>
    </div>

</div>
