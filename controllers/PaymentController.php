<?php

namespace app\controllers;
use Yii;
use app\models\Payment;
use app\models\PaymentSearch;
use app\models\Client;
use app\models\Quote;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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
                            'register-transaction'
                        ],
                        'allow' => true,
                        'allow' => ['?'],
                    ],
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'set-as-invoiced'],
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

    protected function sendEmail($client, $transaction, $order){
        
        if(empty($client) || empty($transaction) || empty($order)) return false;
        
        $message = Yii::$app->mailer
                ->compose(
                    ['html' => "transaction-completed"],
                    ['client' => $client, 'transaction' => $transaction, "order" => $order]
                )
                ->setFrom([Yii::$app->params["infoEmail"]])
                ->setTo($client->email)
                ->setSubject($client->name." ".$client->surname." grazie per aver effettuato il pagamento");

        // $fullFilename = "https://manager.orcidelcilento.it/web/pdf/".$filename;
        // $message->attachContent($fullFilename,['fileName' => $filename,'contentType' => 'application/pdf']); 

        return $message->send();
    }

    public function actionExternalPayment($id_client, $id_quote){
        if(empty($id_client) || empty($id_quote)) return;
        
        $this->layout = "external-payment";

        $id_client  = base64_decode($id_client);
        $id_quote   = base64_decode($id_quote);
        $client = Client::findOne(["id" => $id_client]);
        $quote  = Quote::findOne(["id" => $id_quote]);
        
        if(empty($client) || empty($quote)) return;

        $hasAcconto = Payment::find()->where(["id_quote" => $id_quote])->sum("amount");
        
        // // In case of payment success this will return the payment object that contains all information about the order
        // // In case of failure it will return Null
        // $payment = Yii::$app->PayPalRestApi->processPayment($params);

        return $this->render('external-payment', [
            'client'    => $client,
            'quote'     => $quote,
            'hasAcconto' => $hasAcconto
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
            return $this->render('view', [
                'model' => $model,
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

        return $this->redirect(Yii::$app->request->referrer);
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
