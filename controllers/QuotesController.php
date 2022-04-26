<?php

namespace app\controllers;

use Yii;
use app\models\Quote;
use app\models\QuoteSearch;
use app\models\QuoteDetails;
use app\models\QuoteDetailsSearch;
use app\models\QuotePlaceholder;
use app\models\QuotePlaceholderSearch;
use app\models\Product;
use app\models\Payment;
use app\models\Segnaposto;
use app\models\Packaging;
use app\models\Client;
use app\models\Color;
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
                            'generate-pdf',
                            'choose-quote',
                            'set-confirmed'
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
            $quotePlaceholderModel = new QuotePlaceholderSearch();
            $quotePlaceholderModel->id_quote = $model->id;
            $segnaposto = $quotePlaceholderModel->search([]);
            
            return $this->render('view', [
                'quoteModel'    => $model,
                'quoteDetails'  => $quoteDetails,
                'segnaposto'    => $segnaposto,
                "quotePlaceholderModel" => $quotePlaceholderModel
            ]);
            
        }catch(NotFoundHttpException $e){
            if(empty($model)){
                Yii::$app->session->setFlash('error','Ops...preventivo non trovato');
                $this->redirect("index");
            }
        }
        
    }

    public function actionChooseQuote(){
        return $this->render('choose-quote');
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
                        $quoteDetails->id_color     = isset($model->color[$i]) ? $model->color[$i] : NULL;
                        $quoteDetails->custom_color = isset($model->custom_color[$i]) ? $model->custom_color[$i] : NULL;
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
        $client                 = ["id" => "", "text" => ""];
        $queryParams            = $this->request->queryParams;
        $model->id_client       = isset($queryParams["id_client"]) && !empty($queryParams["id_client"]) ? $queryParams["id_client"] : null;
        $colors                 = Color::find()->select(["id", "label"])->all();
        
        return $this->render('create', [
            'model'         => $model,
            'products'      => $products,
            'packagings'    => $packagings,
            'colors'        => $colors
        ]);
    }

    public function actionConfirm($id){
        try{
            $model = $this->findModel($id);
            $model->confirmed = 1;
            if($model->save()){
                return $this->redirect(['/order/view', "id" => $id]);
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

        if ($this->request->isPost && $model->load($this->request->post())) {
            if($model->save()){
                $i = 0;
                    foreach($model->product as $key => $value){
                        $quoteDetails = new QuoteDetails();
                        $quoteDetails->id_product   = $value;
                        $quoteDetails->id_quote     = $model->id;
                        $quoteDetails->amount       = $model->amount[$i];
                        $quoteDetails->id_packaging = $model->packaging[$i];
                        $quoteDetails->id_color     = isset($model->color[$i]) ? $model->color[$i] : NULL;
                        $quoteDetails->custom_color = isset($model->custom_color[$i]) ? $model->custom_color[$i] : NULL;
                        $quoteDetails->created_at   = date("Y-m-d H:i:s");
                        if(!$quoteDetails->save()){
                            print_r($quoteDetails->getErrors());
                        }
                    }
                Yii::$app->session->setFlash('success', "Aggiornamento completato con successo");
                return $this->redirect(['view', 'id' => $model->id]);    
            }
            
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [QU-102]");
        }

        $detailsModel   = new QuoteDetailsSearch();
        $detailsModel->id_quote = $id;
        $quoteDetails   = $detailsModel->search($this->request->queryParams);
        $segnaposto     = Segnaposto::findOne(["id" => $model->placeholder]);
        $products       = Product::find()->select(["id", "name", "price"])->all();
        $colors         = Color::find()->select(["id", "label"])->all();
        $packagings     = Packaging::find()->select(["id", "label", "price"])->all();
        $currentBottleAmount = QuoteDetails::find()->where(["id_quote" => $id])->sum("amount");
        return $this->render('update', [
            'model' => $model,
            'detailsModel' => $detailsModel,
            'segnaposto'    => $segnaposto,
            'quoteDetails'  => $quoteDetails,
            'products'      => $products,
            'colors'        => $colors,
            'packagings'    => $packagings,
            "currentBottleAmount" => $currentBottleAmount
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
        $quoteDetails   = QuoteDetails::deleteAll(["id_quote" => $id]);
        $payments       = Payment::deleteAll(["id_quote" => $id]);
        $quotePlaceholder = QuotePlaceholder::deleteAll(["id_quote" => $id]);
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
    
        $out["status"] = "200";
        
        return $out;
    }

    public function actionGeneratePdf($id, $flag){
        $quote      = $this->findModel($id);
        
        if(empty($quote)) return;
        
        $quotePlaceholder = QuotePlaceholder::find()->where(["id_quote" => $quote->id])->one();
        $products   = QuoteDetails::findAll(["id_quote" => $quote->id]);
        $colors     = [];
        $i = 0;

        foreach($products as $product){
            $colors[$i] = $product->id_color;
            $i++;
        }

        $colors     = Color::find()->where(["IN", "id", $colors])->all();
        $client     = Client::findOne(["id" => $quote->id_client]);
        $deposit    = Payment::findOne(["id_quote" => $quote->id, "type" => 0]);
        $balance    = 0;

        if($deposit){
            $balance = $deposit < 0 ? 0 : $deposit->formatNumber($quote->total - $deposit->amount);
        }else{
            $balance = $quote->total;
        }

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
        $pdf->SetXY(140, 13); // set the position of the box
        $pdf->Cell(0, 10, $quote->order_number, 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(139, 20); // set the position of the box
        $pdf->Cell(0, 10, $quote->formatDate($quote->created_at), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(132, 34); // set the position of the box
        $pdf->Cell(0, 10, $quote->getClient(), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(149, 41.5); // set the position of the box
        $pdf->Cell(0, 10, $client->phone, 0, 0, 'C'); // add the text, align to Center of cell

        /**
         * PRODUCTS PICS AND INFO
         */
            $x = 15;
            
            $img = 5;
            $line = 160;
            foreach($products as $product){
                $color = Color::findOne(["id" => $product->id_color]);
                $pdf->Image(Yii::getAlias("@webroot")."/".$color->picture, $img, 80, 35, 35);
                $img += 30;

                $item = Product::find()->select(["name", "price"])->where(["id" => $product->id_product])->one(); 
                $pdf->SetXY(10, 120); // set the position of the box
                $pdf->setFontSize("14");
                $pdf->Cell($x, 6, $item->name, 0, 0, 'C'); // add the text, align to Center of cell
                $x += 70;
                
                //summary
                $pdf->setXY(45, $line);
                $pdf->setFontSize("14");
                $pdf->Cell(10, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $item->name." - ".number_format($item->price, 2, ",", ".") ." €")." n. ".$product->amount, 0, 0, 'C'); // add the text, align to Center of cell
                $line += 10;

            }
        /**
         * 
         */


        /**
         * RIEPILOGO ORDINE
         */
            $pdf->setFontSize("12");

            //total
            $pdf->setXY(32, 210);
            $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", number_format($quote->total, 2, ",", ".")." €"), 0, 0, 'C');

            //deposit
            $pdf->setXY(35, 218);
            $pdf->Cell(52, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $deposit  ? number_format($deposit->amount, 2, ",", ".") ." € - ".$quote->formatDate($quote->date_deposit) : ""), 0, 0, 'C');

            //saldo
            $pdf->setXY(35, 226);
            $pdf->Cell(52, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT",  number_format($balance, 2, ",", ".") ." € - ".$quote->formatDate($quote->date_balance)), 0, 0, 'C');

            //shipping
            $pdf->setXY(21, 234);
            $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->shipping ? "SI" : "NO"), 0, 0, 'C');

            if($quote->address){
                $pdf->setXY(20, 242);
                $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->address), 0, 0, 'C');
            }

            $pdf->setXY(44, 257);
            $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->formatDate($quote->deadline)), 0, 0, 'C');

            $pdf->setXY(52, 245);
            $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->custom), 0, 0, 'C');
            
        /**
         * 
         */
         
        /**
         * PLACEHOLDER INFO
         */
        $pdf->setFontSize("12");
        $pdf->setXY(108, 169);
        $pdf->Cell(0, 10, $quote->placeholder ? "SI" : "NO", 0, 0, 'C'); // add the text, align to Center of cell

        if($quotePlaceholder){
            
            // $packaging = Packaging::findOne(["id" => $product->id_packaging]);
            // $pdf->Image(Yii::getAlias("@webroot")."/".$packaging->image, $img, 80, 35, 35);
            
            $pdf->setXY(108, 178);
            $pdf->Cell(0, 10, $quotePlaceholder->getTotal(), 0, 0, 'C'); // add the text, align to Center of cell

            $pdf->setXY(152, 186.4);
            $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", number_format($quotePlaceholder->total, 2, ",", ".")." €"), 0, 0, 'C');

            $pdf->setFontSize("10");
        
            if($deposit){
                $pdf->setXY(156, 195);
                $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $deposit->formatDate($deposit->date_deposit)), 0, 0, 'C');
            }
            
            $pdf->setXY(156, 220);
            $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->formatNumber($quote->balance)), 0, 0, 'C');
        }
        
        

        $tpl = $pdf->importPage(2);
        $pdf->AddPage();
        $pdf->useTemplate($tpl);
        $pdf->setFont("Helvetica");
        $pdf->setFontSize("14");

        $pdf->setFontSize("10");
        //order number
        $pdf->SetXY(140, 13); // set the position of the box
        $pdf->Cell(0, 10, $quote->order_number, 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(139, 20); // set the position of the box
        $pdf->Cell(0, 10, $quote->formatDate($quote->created_at), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(132, 34); // set the position of the box
        $pdf->Cell(0, 10, $quote->getClient(), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(149, 41.5); // set the position of the box
        $pdf->Cell(0, 10, $client->phone, 0, 0, 'C'); // add the text, align to Center of cell

        //LUOGO E DATA
        $pdf->setFontSize("12");
        $pdf->setXY(23, 190);
        $pdf->Cell(30, 0, iconv('UTF-8', "ISO-8859-1//TRANSLIT", "Trentinara, ".$quote->formatDate($quote->created_at)), 0, 0, 'C');

        $pdf->setXY(10, 215);
        $pdf->Cell(30, 0, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->notes), 0, 0, 'C');

        $filename = "ordine_".$quote->order_number."_".$client->name."_".$client->surname.".pdf";
        ob_get_clean();
        
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
        $client = Client::find()->select(["email"])->where(["id" => $model->id_client])->one();
        
        if(empty($client)) return false;

        $message = Yii::$app->mailer
                ->compose(
                    ['html' => $view],
                    ['model' => $model]
                )
                ->setFrom([Yii::$app->params["infoEmail"]])
                ->setTo($client->email)
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
