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
use app\utils\GeneratePdf;
use yii\helpers\Url;
use yii\helpers\Html;
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
                            'set-confirmed',
                            'get-total',
                            'delete-all'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [
                            'create-from-web'
                        ],
                        'allow' => true,
                        'allow' => ['?']
                    ]
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
        $dataProvider->sort->defaultOrder = ["deadline" => SORT_DESC, "order_number" => SORT_ASC];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetTotal($id_quote){
        $out = ["status" => "100", "total" => 0];
        $quote = $this->findModel($id_quote);
        $total = $quote->total;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return !empty($quote) ? $out = ["status" => "200", "total" => $total] : 0;
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
                $model->from_web        = 0;
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
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [QU-101] ".json_encode($model->getErrors()));
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
                $pdf = new GeneratePdf();
                $filename = $pdf->quotePdf($model, $flag, "preventivo");
                $this->sendEmail($model, $filename, "invio-ordine", $model->getClient().", ecco l'ordine delle tue bomboniere L'Uliveto");
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
        return $this->redirect(["index"]);
    }

    public function actionDeleteAll()
    {
        $out = ["status" => "100", "count" => 0];

        $params     = Yii::$app->request->queryParams;
            
        if(isset($params["ids"]) && !empty($params["ids"])){
            $ids = json_decode($params["ids"], true);
            
            foreach($ids as $id){
                
                $model = Quote::findOne(["id" => $id]);
                
                if(!empty($model)){
                    
                    $deleted = $model->delete();
                    
                    if($deleted){
                        $out["count"]++;
                    }
                }
                
            }

            if($out["count"] > 0){
                $out["status"] = "200";
            }
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

    /**
     * create quote from web.
     * this is coming from wordpress website forms
     * it is called in functions.php in wordpress 
     */

     public function actionCreateFromWeb($inputData){
        $out = ["status" => "100", "msg" => "Ops...qualcosa è andato storto"];
        $data = json_decode($inputData, true);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(empty($data)) return $out;

        $latestCode = Quote::find()->select(["order_number"])->orderBy(["created_at" => SORT_DESC])->one();
        $model = new Quote();
        $model->order_number = !empty($latestCode) ? $latestCode->order_number+1 : 1;
        $model->created_at      = date("Y-m-d H:i:s");
        $model->updated_at      = date("Y-m-d H:i:s");
        $model->confirmed       = 0;
        $model->data_evento     = isset($data["Datadelmatrimonio"]) ? $data["Datadelmatrimonio"] : NULL;
        $model->notes           = isset($data["your-message"]) ? $data["your-message"] : NULL;
        $model->custom          = isset($data["custom"]) ? $data["custom"] : NULL;
        $model->from_web        = 1;
        $model->shipping        = 1;

        $client = new Client();
        $client->name = $data["your-name"];
        $client->email = $data["your-email"];
        $client->phone = $data["Telefono"];

        if($client->save())
            $model->id_client = $client->id;
        else{
            print_r($client->getErrors()); die;
        }

        $total_no_vat = 0;
        $model->total_no_vat = $total_no_vat;
        if($model->save()){
            $out = ["status" => "200", "msg" => "Fatto"]; 
        }
    
        $bottles = [
            "monocolor" => count($data["monocolor"]) > 0 ? $data["monocolor"][0] : [],
            "sfumato" => count($data["sfumato"]) > 0 ? $data["monocolor"][0] : []
        ];
            
        print_r($bottles);die;
        foreach($bottles as $bottle => $value){
            $color      = Color::find()->where(["LIKE", "label", $value])->one();
            
            if(!empty($color) && !empty($product)){
                $quoteDetails               = new QuoteDetails();
                $quoteDetails->id_product   = $color->id_product;
                $quoteDetails->id_quote     = $model->id;

                if($bottle == "sfumato"){
                    $quoteDetails->amount       = $data["quantita_sfumato"];
                    $product    = Product::findOne(["LIKE", "name", $data["orcio"]])->one();
                    if(!empty($product)){
                        $total_no_vat += $product->price*$data["quantita_sfumato"];
                    }
                }else{
                    $quoteDetails->amount       = $data["quantita_monocolor"];
                    $product    = Product::findOne(["LIKE", "name", $data["orcio"]." monocolor"])->one();
                    if(!empty($product)){
                        $total_no_vat += $product->price*$data["quantita_monocolor"];
                    }
                }

                $quoteDetails->id_color  = $color->id;
                $packaging = count($data["packaging"]) ? 
                                Packaging::find()
                                    ->where(["LIKE", "label", $data["packaging"][0]])
                                    ->andWhere(["id_product" => $product->id])
                                    ->one()
                            : NULL;

                if($packaging){
                    $quoteDetails->id_packaging = $packaging->id;
                }
                
                $quoteDetails->created_at   = date("Y-m-d H:i:s");
    
                if(!$quoteDetails->save()){
                    print_r($quoteDetails->getErrors());
                }
                $i++;
            }

        }
    
        $model->total_no_vat = $total_no_vat;
        if($model->save()){
            return $out = ["status" => "200", "msg" => "Fatto"]; 
        }
        else{
            print_r($model->getErrors());die;
        }
        
    }

    public function actionGetByClientId($id_client){
        
        if(is_null($id_client)) return;

        $out = ['results' => [], "status" => "100", "quotesPlaceholder" => []];
        
        $data = Quote::find()
                    ->select(["id", "order_number", "created_at", "confirmed"])
                    ->where(["id_client" => $id_client])
                    ->orderBy(["created_at" => SORT_DESC])
                    ->all();
        
        $i = 0;
        $ids = [];
        foreach($data as $item){
            $out["results"][$i]["id"]   = $item->id;
            $out["results"][$i]["text"] = $item->order_number. " - ".$item->formatDate($item->created_at);
            $ids[$i] = $item->id;
            $i++;     
        }
    
        $quotesPlaceholder = QuotePlaceholder::find()
                                    ->select(["id", "created_at"])
                                    ->where(["IN", "id_quote", array_values($ids)])
                                    ->all();
        
        $i = 0;
        foreach($quotesPlaceholder as $item){
            $out["quotesPlaceholder"][$i]["id"]   = $item->id;
            $out["quotesPlaceholder"][$i]["text"] = $item->id. " - ".$item->formatDate($item->created_at);
            $ids[$i] = $item->id;
            $i++;     
        }
        
        $out["status"] = "200";
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

    public function actionGeneratePdf($id, $flag){
        $quote      = $this->findModel($id);
        
        if(empty($quote)) return;
        
        $pdf = new GeneratePdf();
        $filename = $pdf->quotePdf($quote, $flag, "preventivo", "preventivi");

        if($flag == "send"){
            if($this->sendEmail($quote, $filename, "invio-preventivo", $quote->getClient().", ecco il preventivo delle tue bomboniere L'Uliveto")){
                Yii::$app->session->setFlash('success', "Email con PDF allegato inviato correttamente: ".$filename);
            }else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong");
            }
        }else{
            $url = Html::a("Scarica", Url::to(["/pdf/preventivi/".$filename]));

            Yii::$app->session->setFlash('success', "Pdf generato correttamente.".$url);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function sendEmail($model, $filename, $view, $object){
        
        if(empty($model)) return false;
        $client = Client::find()->select(["email"])->where(["id" => $model->id_client])->one();
        
        if(empty($client)) return false;

        $message = Yii::$app->mailer
                ->compose(
                    ['html' => $view],
                    ['model' => $model]
                )
                ->setFrom([Yii::$app->params["infoEmail"] => Yii::$app->params["infoName"]])
                ->setTo($client->email)
                ->setSubject($object);

        $fullFilename = "https://manager.orcidelcilento.it/web/pdf/preventivi/".$filename;
        // $message->attachContent("Preventivo", ['fileName' => $filename,'contentType' => 'application/pdf']); 
        $message->attach($fullFilename);

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
