<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <div class="card">
        <div class="card-header">
            <?= Html::a('<i class="fas fa-pencil-alt"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Cancella', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Sei sicuro di voler cancellare questo elemento?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
        <div class="card-body table-responsive">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'nome',
                    'username',
                    'email:email',
                    [
                        'attribute' => "picture",
                        'value' => function($model){
                            return Html::img(Url::to(Yii::getAlias("@web")."/images/users/".$model->picture), ["class" => "img-responsive"]);
                        },
                        'format' => "raw"
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function($model){
                            return $model->getStatus();
                        }
                    ],
                    [
                        'attribute' => 'role',
                        'value' => function($model){
                            return $model->roleList[$model->role];
                        }
                    ],
                    'created_at',
                    'updated_at',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
