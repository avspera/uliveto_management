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
                            'upload-allegato'
                        ],
                        'allow' => true,
                        'allow' => ['?'],
                    ],
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'set-as-invoiced', 'has-acconto', 'send-email-payment'],
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

    public function actionHasAcconto($id_quote){
        $out = ["status" => "100", "hasAcconto" => false, "amount" => 0];

        $acconto = Payment::findOne(["id_quote" => $id_quote, "type" => 0]);
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return !empty($acconto) ? $out = ["status" => "200", "hasAcconto" => true, "amount" => floatval($acconto->amount)] : $out;
    }

    public function actionUploadAllegato(){
        
        if ($this->request->isPost) {
            $postData =  $this->request->post();
            if(isset($postData["Payment"]["id"])){
                $id = $postData["Payment"]["id"];
                $model = Payment::findOne(["id" => $id]);
                $model->allegato = $this->manageUploadFiles($model);
                
                if($model->save()){
                    Yii::$app->session->setFlash('success', "Operazione completata con successo. Grazie");
                }else{
                    
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [UPLOAD_FILE-103]");
                }    
            }
            
            return $this->redirect(Yii::$app->request->referrer);
            
        }
    }


    public function actionRegisterTransaction($transaction, $id_client, $id_quote){
        if(empty($transaction) || empty($id_client) || empty($id_quote)) return;
        
        $transaction = json_decode($transaction, true);
        
        if(empty($transaction)) return;

        $out = ["status" => "100", "msg" => "Ops...c'è qualcosa che non va"];
        $payment = new Payment();
        $payment->id_client = $id_client;
        $payment->id_quote  = $id_quote;
        $payment->amount    = isset($transaction["amount"]["value"]) ? $transaction["amount"]["value"] : 0;
        $payment->created_at = date("Y-m-d H:i:s");
        $payment->type      = 0;
        $payment->fatturato = 0;
        $payment->payed     = 1;
        $payment->id_transaction = $transaction["id"];

        if($payment->save()){
            $out = ["status" => "200", "msg" => "Transazione ".$transaction["id"] ." completata con successo. Riceverai a breve un'email di conferma"];
            $client = Client::find()->select(["name", "surname", "email"])->where(["id" => $id_client])->one();
            $order = Quote::findOne(["id" => $id_quote]);
            // return $this->render("@app/mail/transaction-completed", ["client" => $client, "transaction" => $transaction, "order" => $order]);
            $this->sendEmail($client, $transaction, $order);
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

    public function actionSendEmailPayment($id_client, $id_quote, $id_payment = []){
        if(empty($id_client) || empty($id_quote)) return;
        
        $this->layout = "external-payment";

        $client     = Client::findOne(["id" => $id_client]);
        $orderQuote  = Quote::findOne(["id" => $id_quote]);
        $orderPlaceholder = QuotePlaceholder::findOne(["id" => $id_quote]);
        
        if(empty($client)) return;

        $order = !empty($orderQuote) ? $orderQuote : $orderPlaceholder;
        
        if($this->sendEmail($client, [], $order, $id_payment)){
            Yii::$app->session->setFlash('success', "Email inviata correttamente");
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [ORDER_SEND_EMAIL-100]");
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function sendEmail($client, $transaction = [], $order, $id_payment){
        
        if(empty($client) || empty($order)) return false;
        
        $subject = !empty($transaction) ? $client->name." ".$client->surname." grazie per aver effettuato il pagamento" : $client->name." ".$client->surname." procedi per effettuare il tuo pagamento";
        $message = Yii::$app->mailer
                ->compose(
                    ['html' => !empty($transaction) ? "transaction-completed" : "send-payment"],
                    ['client' => $client, 'transaction' => $transaction, "order" => $order, "id_payment" => $id_payment]
                )
                ->setFrom([Yii::$app->params["infoEmail"]])
                ->setTo($client->email)
                ->setSubject($subject);

        $pdf = new GeneratePdf();
        $id_quote = !empty($payment->id_quote) ? $payment->id_quote : $order->id;
        $quote = Quote::findOne(["id" => $id_quote]);
        $filename = $pdf->quotePdf($quote,"F", "ordine");
        
        $fullFilename = "https://manager.orcidelcilento.it/web/pdf/".$filename;
        $message->attachContent($fullFilename,['fileName' => $filename,'contentType' => 'application/pdf']); 

        return $message->send();
    }

    protected function manageUploadFiles($model) {

        $uploader   = new FKUploadUtils();
        $path       = Yii::getAlias('@webroot')."/uploads/payments/";
    
        $dirCreated = FileHelper::createDirectory($path);
        
        $allegato = UploadedFile::getInstance($model, 'allegato');
        if (!empty($allegato)){
            $filename = $uploader->generateAndSaveFile($allegato, $path);
            $model->allegato = "uploads/payments/".$filename;
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
                $model->fatturato   = 0;
                $model->payed       = 0;
                
                if($model->save())
                    return $this->redirect(['view', 'id' => $model->id]);
                else{
                    print_r($model->getErrors());die;
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [PACK-102]");
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
