<?php

namespace app\controllers;

use app\models\Sales;
use app\models\SalesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * SalesController implements the CRUD actions for Sales model.
 */
class SalesController extends Controller
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
                            'get-by-id'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Sales models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SalesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetById($id){
        
        $sale = $this->findModel($id);
        
        if(empty($sale)) return;
        $out = ['amount' => 0, "status" => "100"];

        if(!empty($sale)){
            $out["status"] = "200";
            $out["amount"] = $sale->amount;
        }
            
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return $out;
    }
    /**
     * Displays a single Sales model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if($model = $this->findModel($id)){
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [SALES-100]");
            return $this->redirect(['index']);
        }

    }

    /**
     * Creates a new Sales model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Sales();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->created_at = date("Y-m-d H:i:s");
                if($model->save()){
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                else{
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [SALES-101]");
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
     * Updates an existing Sales model.
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
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [SALES-102]");
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sales model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', "Elemento cancellato con successo");
        return $this->redirect(['index']);
    }

    /**
     * Finds the Sales model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Sales the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sales::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
