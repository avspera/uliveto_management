<?php

namespace app\controllers;

use app\models\Segnaposto;
use app\models\SegnapostoSearch;
use app\models\Quote;
use app\models\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * SegnapostoController implements the CRUD actions for Segnaposto model.
 */
class SegnapostoController extends Controller
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
                            'search-from-select' 
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionSearchFromSelect($q = ""){
        $term = $q;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => [], "status" => "100"];
        
        if (!is_null($term)) {
            $clients = Quote::find()
                        ->select(["quote.order_number", "quote.id", "quote.id_client"])
                        ->innerJoin('client', '`client`.`id` = `quote`.`id_client`')
                        ->where(["LIKE", "surname", $term])
                        ->orWhere(["LIKE", "name", $term])
                        ->orderBy(["name" => SORT_ASC])
                        ->all();
            
            $i = 0;
            foreach($clients as $item){
                $client = Client::findOne($item->id_client);
                $out["results"][$i]["id"]   = $item->id;
                $out["results"][$i]["text"] = $item->order_number." - ".$client->name." ".$client->surname;
                $i++;
            }
            
            if(!empty($out["results"])){
                $out["status"] = "200";
            }
        }
       
        return $out;
    }
    /**
     * Lists all Segnaposto models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SegnapostoSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Segnaposto model.
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
     * Creates a new Segnaposto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Segnaposto();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) ) {
                $model->created_at = date("Y-m-d H:i:s");
                if($model->save())
                    return $this->redirect(['view', 'id' => $model->id]);
                else{
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [SEGNAPOSTO-101]");
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
     * Updates an existing Segnaposto model.
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
     * Deletes an existing Segnaposto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Segnaposto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Segnaposto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Segnaposto::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
