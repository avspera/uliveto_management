<?php

namespace app\controllers;

use Yii;
use app\models\Quote;
use app\models\QuoteSearch;
use app\models\QuoteDetails;
use app\models\QuoteDetailsSearch;
use app\models\Product;
use app\models\Packaging;
use app\models\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;
use Mpdf\Mpdf;
use \setasign\Fpdi\Fpdi;
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

        if ( $action->id == 'error' ) {
            $this->layout = 'error';
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
        try{
            $model = $this->findModel($id);
        
            $detailsModel = new QuoteDetailsSearch();
            $detailsModel->id_quote = $id;
            $quoteDetails = $detailsModel->search($this->request->queryParams);
            
            return $this->render('view', [
                'model'         => $model,
                'quoteDetails'  => $quoteDetails
            ]);
        }catch(NotFoundHttpException $e){
            if(empty($model)){
                Yii::$app->session->setFlash('error','Ops...preventivo non trovato');
                $this->redirect("index");
            }
        }
        
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
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [QU-101]");
                }
            }
        } else {
            $model->loadDefaultValues();
        }
        
        $products   = Product::find()->select(["id", "name", "price"])->orderBy(["name" => SORT_ASC])->all();
        $packagings = Packaging::find()->select(["id", "label", "price"])->orderBy(["label" => SORT_ASC])->all(); 
    
        $model->total_no_vat    = 0;
        $model->total           = 0;
        return $this->render('create', [
            'model'         => $model,
            'products'      => $products,
            'packagings'    => $packagings,
        ]);
    }

    public function actionConfirm($id){
        try{
            $model = $this->findModel($id);
            $model->confirmed = 1;
            if($model->save()){
                return $this->render('view', [
                    'model' => $model,
                ]);
            }
        }catch(Exception $e){
            Yii::$app->session->setFlash('error', "Ops...something went wrong [QU-103]");
            
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
            Yii::$app->session->setFlash('success', "Aggiornamento completato con successo");
            return $this->redirect(['view', 'id' => $model->id]);
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [QU-102]");
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
        Yii::$app->session->setFlash('success', "Preventivo cancellato con successo");
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

    public function actionGeneratePdf($id, $flag){
        $quote  = $this->findModel($id);
        
        if(empty($quote)) return;

        $client = Client::findOne(["id" => $quote->id_client]);
        
        ob_start();
        $pdf = new FPDI();
        
        // Reference the PDF you want to use (use relative path)
        $pagecount = $pdf->setSourceFile(Yii::getAlias("@webroot").'/pdf/preventivo.pdf');

        // Import the first page from the PDF and add to dynamic PDF
        $tpl = $pdf->importPage(1);
        $pdf->AddPage();
        $pdf->useTemplate($tpl);
        $pdf->SetFont('Helvetica');

        $pdf->setFontSize("10");
        //order number
        $pdf->SetXY(100, 18); // set the position of the box
        $pdf->Cell(0, 10, $quote->order_number, 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(113, 25); // set the position of the box
        $pdf->Cell(0, 10, $quote->formatDate($quote->created_at), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(121, 31); // set the position of the box
        $pdf->Cell(0, 10, $quote->getClient(), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(116, 38); // set the position of the box
        $pdf->Cell(0, 10, $client->phone, 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(-296, -58); // set the position of the box
        $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", number_format($quote->total, 2, ",", ".")." €"), 0, 0, 'C');

        $pdf->SetXY(-296, -51); // set the position of the box
        $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", number_format($quote->deposit, 2, ",", ".")." €"), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(-294, -44); // set the position of the box
        $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", number_format($quote->balance, 2, ",", ".")." €". " - "), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(-133, -51); // set the position of the box
        $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->shipping == 0 ? "NO" : "SI"), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(-121, -44); // set the position of the box
        $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->formatDate($quote->deadline)), 0, 0, 'C'); // add the text, align to Center of cell

        $filename = "preventivo_".$quote->order_number."_".$client->name."_".$client->surname.".pdf";
        ob_clean();

        $pdf->Output($filename, $flag == "send" ? 'F' : 'D');    

        if($flag == "send"){
            if($this->sendEmail($quote, $filename, "invio-preventivo")){
                Yii::$app->session->setFlash('success', "Pdf inviato correttamente");
            }else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong");
            }

            return $this->redirect("index");
        }
        
    }

    protected function sendEmail($model, $filename, $view){
        
        if(empty($model)) return false;
        
        $message = Yii::$app->mailer
                ->compose(
                    ['html' => $view],
                    ['model' => $model]
                )
                ->setFrom([Yii::$app->params["infoEmail"]])
                ->setTo("antoniovincenzospera@gmail.com")
                ->setSubject($model->getClient()." ecco il tuo preventivo");

        $fullFilename = "https://manager.orcidelcilento.it/web/pdf/".$filename;
        $message->attachContent($fullFilename,['fileName' => $filename,'contentType' => 'application/pdf']); 

        return $message->send();
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
