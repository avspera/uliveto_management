<?php

namespace app\controllers;

use Yii;
use app\models\InviaCatalogo;
use app\models\InviaCatalogoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * InviaCatalogoController implements the CRUD actions for InviaCatalogo model.
 */
class InviaCatalogoController extends Controller
{
     /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index', 
                            'view', 
                            'update', 
                            'delete', 
                            'create',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new \Exception('You are not allowed to access this page');
                }
            ],
        ];
    }

    /**
     * Lists all InviaCatalogo models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new InviaCatalogoSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InviaCatalogo model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new InviaCatalogo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new InviaCatalogo();

        if ($this->request->isPost) {
            
            if ($model->load($this->request->post())) {
                $model->created_at = date("Y-m-d H:i:s");
                if($model->save() && $this->sendEmail($model)){
                    return $this->redirect(['view', 'id' => $model->id]);
                }   
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing InviaCatalogo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing InviaCatalogo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function sendEmail($model){
        
        if(empty($model)) return false;

        return Yii::$app->mailer
                ->compose(
                    ['html' => 'invio-catalogo'],
                    ['model' => $model]
                )
                ->setFrom(["ordini@opostomio.it"])
                ->setTo($user->email)
                ->setSubject('Ordine '.$model->id.' completato - \'OPostoMio.it')
                ->send();
    }

    /**
     * Finds the InviaCatalogo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return InviaCatalogo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InviaCatalogo::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
