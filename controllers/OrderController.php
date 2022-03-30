<?php

namespace app\controllers;

use Yii;
use app\models\Quote;
use app\models\QuoteSearch;
use app\models\QuoteDetailsSearch;
use app\models\PaymentSearch;
use app\models\Product;
use app\models\Segnaposto;
use app\models\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
/**
 * OrderController implements the CRUD actions for Quote model.
 */
class OrderController extends Controller
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

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            if(Yii::$app->user->identity->role == 0){
                return true;
            }else{
                return false;
            }
        }

        return true;
    }

    /**
     * Lists all Quote models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuoteSearch();
        $searchModel->confirmed = 1;
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->sort->defaultOrder = ["created_at" => SORT_DESC];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Quote model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if(!$model){
            Yii::$app->session->setFlash('error', "Ops...something went wrong [OR-100]");
            return $this->render('index');
        }
        $client = Client::find()->select(["name", "surname"])->where(["id" => $model->id_client])->one();
        
        $detailsModel = new QuoteDetailsSearch();
        $detailsModel->id_quote = $id;
        $products = $detailsModel->search($this->request->queryParams);
        $segnaposto = Segnaposto::findOne(["id" => $model->placeholder]);
        $paymentModel = new PaymentSearch();
        $paymentModel->id_quote = $id;
        $payments     = $paymentModel->search([]);
        $payments->sort->defaultOrder = ["created_at" => SORT_DESC];

        return $this->render('view', [
            'model'     => $model,
            'client'    => !empty($client) ? $client->name." ".$client->surname : "",
            'products'  => $products,
            'payments'  => $payments,
            'segnaposto' => $segnaposto
        ]);
    }

    /**
     * Creates a new Quote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model      = new Quote();
        $latestCode = Quote::find()->select(["order_number"])->orderBy(["created_at" => SORT_DESC])->one();
        $model->order_number = !empty($latestCode) ? $latestCode->order_number+1 : 1;
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->created_at = date("Y-m-d H:i:s");
                $model->updated_at = date("Y-m-d H:i:s");
                $model->confirmed = 1;
                if($model->save())
                    return $this->redirect(['view', 'id' => $model->id]);
                else{
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [OR-101]");
                }
            }
        } else {
            $model->loadDefaultValues();
        }
        
        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Quote model.
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
     * Deletes an existing Quote model.
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

    public function actionGetByClientId($id_client){
        
        if(is_null($id_client)) return;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => [], "status" => "100"];
        
        $data = Quote::find()
                    ->select(["id", "created_at"])
                    ->where(["id_client" => $id_client])
                    ->orderBy(["created_at" => SORT_DESC])
                    ->all();
            
        $i = 0;
        foreach($data as $item){
            $out["results"][$i]["id"]   = $item->id;
            $out["results"][$i]["text"] = $item->id. " - ".$item->formatDate($item->created_at);
            $i++;
        }
        
        if(!empty($out["results"]))
            $out["status"] = "200";
        return $out;
    }

    /**
     * Finds the Quote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Quote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Quote::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
