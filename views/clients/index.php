<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use kartik\grid\GridView;

$this->title = 'Clienti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

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
