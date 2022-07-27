<?php

namespace app\controllers;
use Yii;
use app\models\Payment;
use app\models\PaymentSearch;
use app\models\Client;
use app\models\Quote;
use app\models\QuotePlaceholder;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\utils\GeneratePdf;
use app\utils\FKUploadUtils; 
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends Controller
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
                            'external-payment', 
                            'register-transaction',
                            'upload-allegato',
                            'generate-pdf',
                            'delete-all'
                        ],
                        'allow' => true,
                        'allow' => ['?'],
                    ],
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'check-saldo-payment',
                                    'delete', 'set-as-invoiced', 'get-amount', 'send-email-payment'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => "error"
                    ]
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            if(Yii::$app->user->identity->role == 0 || Yii::$app->user->identity->role == 3){
                return true;
            }else{
                return false;
            }
        }

        return true;
    }

    public function actionDeleteAll()
    {
        $out = ["status" => "100", "count" => 0];

        $params     = Yii::$app->request->queryParams;
            
        if(isset($params["ids"]) && !empty($params["ids"])){
            $ids = json_decode($params["ids"], true);
            
            foreach($ids as $id){
                
                $model = Payment::findOne(["id" => $id]);
                
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

    public function actionGetAmount($id_quote, $type, $flag){
        $out = ["status" => "100", "hasAcconto" => false, "amount" => 0];

        $amount = 0;
        if($type == 0){
            if($flag == "quote")
                $quote  = Quote::findOne(["id" => $id_quote]);
            else{
                $quote = QuotePlaceholder::findOne(["id" => $id_quote]);
            }

            $amount = $flag == "quote" ? $quote->deposit : $quote->acconto;
            $hasAcconto = true;

        }else{
            if($flag == "quote")
                $balance = Quote::find()->select(["balance"])->where(["id" => $id_quote])->one();
            else{
                $balance = QuotePlaceholder::find()->select(["saldo"])->where(["id" => $id_quote])->one();
            }
            
            $amount = $flag == "quote" ? $balance->balance : $balance->saldo;
        }
    
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return !empty($amount) ? $out = ["status" => "200", "hasAcconto" => $hasAcconto, "amount" => $amount] : $out;
    }

    public function actionUploadAllegato(){
        
        if ($this->request->isPost) {
            $postData =  $this->request->post();
            if(isset($postData["Payment"]["id"])){
                $id = $postData["Payment"]["id"];
                $model = Payment::findOne(["id" => $id]);
                if (!empty($_FILES)) {
                    $model->allegato = $this->manageUploadFiles($model);
                }
                
                if($model->save()){
                    Yii::$app->session->setFlash('success', "Operazione completata con successo. Grazie");
                }else{
                    
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [UPLOAD_FILE-103]");
                }    
            }
            
            return $this->redirect(Yii::$app->request->referrer);
            
        }
    }


    public function actionRegisterTransaction($transaction, $id_client, $id_quote, $id_payment){
        if(empty($transaction) || empty($id_client) || empty($id_quote)) return;
        
        $transaction = json_decode($transaction, true);
        
        if(empty($transaction)) return;

        $out = ["status" => "100", "msg" => "Ops...c'è qualcosa che non va"];
        $payment = Payment::findOne(["id" => $id_payment]);
        $payment->amount    = isset($transaction["amount"]["value"]) ? $transaction["amount"]["value"] : 0;
        $payment->created_at = date("Y-m-d H:i:s");
        $payment->fatturato = 0;
        $payment->payed     = 1;
        $payment->transaction = $transaction["id"];

        if($payment->save()){
            $out = ["status" => "200", "msg" => "Transazione ".$transaction["id"] ." completata con successo. Riceverai a breve un'email di conferma"];
            $client = Client::find()->select(["name", "surname", "email"])->where(["id" => $id_client])->one();
            $order  = Quote::findOne(["id" => $id_quote]);
            // return $this->render("@app/mail/transaction-completed", ["client" => $client, "transaction" => $transaction, "order" => $order]);
            $this->sendEmail($order, $transaction, "", $client, $payment->id);
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

    public function actionSendEmailPayment($id_client, $id_quote, $id_payment = []){
        if(empty($id_client) || empty($id_quote)) return;
        
        // $this->layout = "external-payment";
        $client     = Client::findOne(["id" => $id_client]);
        $orderQuote  = Quote::findOne(["id" => $id_quote]);
        $orderPlaceholder = QuotePlaceholder::findOne(["id" => $id_quote]);
        
        if(empty($client)) return;

        $order = !empty($orderQuote) ? $orderQuote : $orderPlaceholder;
        
        if($this->sendEmail($order, [], $filename, $client, $id_payment, "nonconfirm")){
            Yii::$app->session->setFlash('success', "Email inviata correttamente");
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [ORDER_SEND_EMAIL-100]");
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionGeneratePdf($id, $flag, $id_payment){
        
        $model = $this->findModel($id);
        $quote      = Quote::findOne(["id" => $model->id_quote]);
        
        if(empty($quote)){
            $quote      = QuotePlaceholder::findOne(["id" => $model->id_quote_placeholder]);
            $quote   = Quote::findOne(["id" => $quote->id_quote]);
            $client     = Client::findOne(["id" => $quote->id_client]);
        }else{
            $client     = Client::findOne(["id" => $quote->id_client]);
        }

        if(empty($quote)) return;
        
        $pdf = new GeneratePdf();
        $filename = $pdf->quotePdf($quote, $flag, "preventivo", "preventivi");
        
        if($flag == "send"){
            if($this->sendEmail($quote, [], $filename, $client, $id_payment, "nonconfirm")){
                Yii::$app->session->setFlash('success', "Email con PDF allegato inviato correttamente: ".$filename);
            }else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong");
            }
        }else{
            Yii::$app->session->setFlash('success', "Pdf generato correttamente.<a href='/web/pdf/preventivi/".$filename."'>Scarica</a>");
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function sendEmail($order, $transaction = [], $filename, $client, $id_payment = "", $flag = "confirm"){
        
        if(empty($client) || empty($order)) return false;
        
        if(!empty($transaction)) 
            $flag = "confirm";
        
        $subject = $flag == "confirm" ? $client->name." ".$client->surname.", grazie per aver effettuato il pagamento" : $client->name." ".$client->surname." procedi per effettuare il tuo pagamento";
        $message = Yii::$app->mailer
                ->compose(
                    ['html' => $flag == "confirm" ? "transaction-completed" : "send-payment"],
                    ['client' => $client, 'transaction' => $transaction, "order" => $order, "id_payment" => $id_payment]
                )
                ->setFrom([Yii::$app->params["infoEmail"] => Yii::$app->params["infoName"]])
                ->setTo($client->email)
                ->setSubject($subject);

        $pdf = new GeneratePdf();
        $filename   = $pdf->quotePdf($order, "F", "ordine");
        
        $fullFilename = "https://manager.orcidelcilento.it/web/pdf/ordini/".$filename;
        
        // $message->attachContent($fullFilename,['fileName' => $filename,'contentType' => 'application/pdf']); 
        $message->attach($fullFilename);

        

        return $message->send();
    }

    protected function manageUploadFiles($model) {

        $uploader   = new FKUploadUtils();
        $path       = Yii::getAlias('@webroot')."/uploads/payments/";
    
        $dirCreated = FileHelper::createDirectory($path);
        
        $allegato = UploadedFile::getInstances($model, 'allegato');
        if (!empty($allegato)){
            $files = [];
            $i = 0;
        
            foreach($allegato as $attachment){
                $filename   = $uploader->generateAndSaveFile($attachment, $path);
                $files[$i]  = "uploads/payments/".$filename;
                $i++;
            }
            
            $model->allegato = json_encode($files);
        }
        
        return $model->allegato;
    }

    public function actionExternalPayment($id_client, $id_quote, $id_payment){
        if(empty($id_client) || empty($id_quote) || empty($id_payment)) return;
        
        $this->layout = "external-payment";

        $id_client  = base64_decode($id_client);
        $id_quote   = base64_decode($id_quote);
        $id_payment   = base64_decode($id_payment);

        $client = Client::findOne(["id" => $id_client]);
        $payment  = $this->findModel($id_payment);

        $quote  = Quote::findOne(["id" => $id_quote]);
        if(empty($quote)){
            $quote = QuotePlaceholder::findOne(["id" => $id_quote]);
        }

        if(empty($client) || empty($quote)) return;
        
        // // In case of payment success this will return the payment object that contains all information about the order
        // // In case of failure it will return Null
        // $payment = Yii::$app->PayPalRestApi->processPayment($params);

        return $this->render('external-payment', [
            'client'    => $client,
            'quote'     => $quote,
            'payment'   => $payment
        ]);
    }

    /**
     * Lists all Payment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->sort->defaultOrder = ["created_at" => SORT_DESC];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSetAsInvoiced($id){
        $payment = $this->findModel($id);
        if(!empty($payment)){
            $payment->fatturato = 1;
            $payment->save();
        }

        return $this->render('view', [
            "model" => $payment
        ]);
    }

    /**
     * Displays a single Payment model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if($model = $this->findModel($id)){
            if(!empty($model->id_quote)){
                $quote = Quote::findOne(["id" => $model->id_quote]);
                if(!empty($quote))
                    $client = $quote->getClient();
            }else{
                $quotePlaceholder = QuotePlaceholder::findOne(["id" => $model->id_quote_placeholder]);
                $quote = Quote::findOne(["id" => $quotePlaceholder->id_quote]);
                if(!empty($quote)){
                    $client = $quote->getClient();
                }
            }
            
            return $this->render('view', [
                'model' => $model,
                'id_client' => $client
            ]);
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [PACK-100]");
            return $this->redirect("index");
        }
        
    }

    /**
     * Creates a new Payment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Payment();
        
        $error = false;
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->created_at  = date("Y-m-d H:i:s");
                if(empty($model->fatturato)){
                    $model->fatturato   = 0;    
                }

                if(empty($model->payed)){
                    $model->payed       = 0;
                }
                
                if($model->save())
                    return $this->redirect(['view', 'id' => $model->id]);
                else{
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [PACK-101]");
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
     * Updates an existing Payment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load($this->request->post())) {

            if($model->save()){
                if($model->payed == 1){
                    $client = Client::find()->select(["name", "surname", "email"])->where(["id" => $model->id_client])->one();
                    if(!empty($model->id_quote)){
                        $order  = Quote::findOne(["id" => $model->id_quote]);
                    }else{
                        $order  = QuotePlaceholder::findOne(["id" => $model->id_quote_placeholder]);
                    }
                    $pdf = new GeneratePdf();
                    $filename = $pdf->quotePdf($quote, $flag, "ordine");
                    $this->sendEmail($order, [], $filename, $client, $id_payment = $model->id, "confirm");
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong [PACK-102]");
            }   
        }

        if(!empty($model->id_quote)){
            $quote = Quote::find()->select(["id", "total"])->where(["id" => $model->id_quote])->one();
            $model->id_quote = $quote->id;
        }
        
        if(!empty($model->id_quote_placeholder)){
            $quotePlaceholder = QuotePlaceholder::find()->select(["id"])->where(["id" => $model->id_quote_placeholder])->one();
            if(!empty($quotePlaceholder)){
                $model->id_quote_placeholder = $quotePlaceholder->id;
            }
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Payment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', "Elemento cancellato con successo");

        return $this->redirect(["index"]);
    }

    /**
     * Finds the Payment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Payment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payment::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
