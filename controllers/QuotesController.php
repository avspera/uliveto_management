<?php

namespace app\controllers;

use Yii;
use app\models\Quote;
use app\models\QuoteSearch;
use app\models\QuoteDetails;
use app\models\QuoteDetailsSearch;
use app\models\Product;
use app\models\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
/**
 * QuotesController implements the CRUD actions for Quote model.
 */
class QuotesController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
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
                            'get-by-client-id',
                            'confirm',
                            'generate-pdf'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            if(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 1){
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
        $searchModel->confirmed = 0;
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->sort->defaultOrder = ["deadline" => SORT_DESC];
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
        $detailsModel = new QuoteDetailsSearch();
        $detailsModel->id_quote = $id;
        $quoteDetails = $detailsModel->search($this->request->queryParams);
        
        return $this->render('view', [
            'model'         => $model,
            'quoteDetails'  => $quoteDetails
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
                
                $model->created_at      = date("Y-m-d H:i:s");
                $model->updated_at      = date("Y-m-d H:i:s");
                $model->confirmed       = 0;
                $model->total_no_vat    = $model->total / (1 + 4 / 100);
                if($model->save()){
                    $i = 0;
                    
                    foreach($model->product as $key => $value){
                        $quoteDetails = new QuoteDetails();
                        $quoteDetails->id_product   = $value;
                        $quoteDetails->id_quote     = $model->id;
                        $quoteDetails->amount       = $model->amount[$i];
                        $quoteDetails->id_packaging = $model->packaging[$i];
                        $quoteDetails->id_color     = $model->color[$i];
                        $quoteDetails->created_at   = date("Y-m-d H:i:s");
                        if(!$quoteDetails->save()){
                            print_r($quoteDetails->getErrors());
                        }
                        $i++;
                    }

                    return $this->redirect(['view', 'id' => $model->id]);
                }
                    
                else{
                    print_r($model->getErrors());die;
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [QU-101]");
                }
            }
        } else {
            $model->loadDefaultValues();
        }
        
        return $this->render('create', [
            'model' => $model
        ]);
    }

    public function actionConfirm($id){
        $model = $this->findModel($id);
        $model->confirmed = 1;
        if($model->save()){
            return $this->render('view', [
                'model' => $model,
            ]);
        }
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

    public function actionGeneratePdf($id){
        $quote  = $this->findModel($id);
        
        if(empty($quote)) return;

        $client = Client::findOne(["id" => $quote->id_client]);
        
        $content = $this->renderPartial('snippets/_pdf', [
            'quote' => $quote,
            'client' => $client
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE, 
            // A4 paper format
            'format' => Pdf::FORMAT_A4, 
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER, 
            // your html content input
            'content' => $content,  
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}', 
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            'methods' => [ 
                'SetHeader'=>['Krajee Report Header'], 
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);
        
        // if($this->sendEmail())
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
