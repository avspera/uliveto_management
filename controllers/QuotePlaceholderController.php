<?php

namespace app\controllers;

use Yii;
use app\models\Quote;
use app\models\QuotePlaceholder;
use app\models\QuotePlaceholderSearch;
use app\models\Segnaposto;
use app\models\Client;
use app\utils\GeneratePdf;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * QuotePlaceholderController implements the CRUD actions for QuotePlaceholder model.
 */
class QuotePlaceholderController extends Controller
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
                            'get-total',
                            'generate-pdf',
                            'send-email-payment',
                            'confirm',
                            'search-for-clients-from-select'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all QuotePlaceholder models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new QuotePlaceholderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuotePlaceholder model.
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
     * Creates a new QuotePlaceholder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($id_quote = "")
    {
        $model = new QuotePlaceholder();

        if(!empty($id_quote)){
            $model->id_quote = $id_quote;
        }
        
        if ($this->request->isPost) {
            
            if ($model->load($this->request->post())) {
                
                $model->created_at = date("Y-m-d H:i:s");
                
                if($model->save())
                    return $this->redirect(['view', 'id' => $model->id]);
                else{
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [QUOTE-PLAC-101]");
                    
                }
            }
        } else {
            $model->loadDefaultValues();
        }
        $placeholders = Segnaposto::find()->select(["id", "label", "price"])->all();
        return $this->render('create', [
            'model'         => $model,
            'placeholders'  => $placeholders
        ]);
    }

    public function actionConfirm($id){
        try{
            $model = $this->findModel($id);
            $model->confirmed = 1;
            if($model->save()){
                // $pdf = new GeneratePdf();
                // $filename = $pdf->quotePdf($model, $flag, "preventivo");
                Yii::$app->session->setFlash('success', "Preventivo segnaposto confermato con successo");
                // $this->sendEmail($model, $filename, "invio-ordine-placeholder", $model->getClient().", ecco l'ordine dei tuoi segnaposto L'Uliveto");
                return $this->redirect(Yii::$app->request->referrer);
            }else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong [QU-107]");
                return $this->redirect(Yii::$app->request->referrer);
            }
        }catch(Exception $e){
            Yii::$app->session->setFlash('error', "Ops...something went wrong [QU-103]");
            
            return $this->render('view', [
                'model' => $model,
            ]);
        }
        
    }

    /**
     * Updates an existing QuotePlaceholder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->updated_at = date("Y-m-d H:i:s");
            if($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
        }
        $placeholders = Segnaposto::find()->select(["id", "label", "price"])->all();
        return $this->render('update', [
            'model'         => $model,
            'placeholders'  => $placeholders
        ]);
    }

    public function actionGeneratePdf($id, $flag){
        $quotePlaceholder      = $this->findModel($id);
        
        if(empty($quotePlaceholder)) return;
        
        $pdf        = new GeneratePdf();
        $filename   = $pdf->quotePdf($quotePlaceholder, $flag, "preventivo", "preventivi");
        
        $quote      = Quote::findOne(["id" => $quotePlaceholder->id_quote]);
        $client     = Client::findOne(["id" => $quote->id_client]);
        if($flag == "send"){
            
            if($this->sendEmail($quotePlaceholder, $filename, "invio-preventivo-segnaposto", $quote->getClient().", ecco il preventivo delle tue bomboniere L'Uliveto", $client)){
                Yii::$app->session->setFlash('success', "Email con PDF allegato inviato correttamente: ".$filename." - email: ".$client->email);
            }else{

                Yii::$app->session->setFlash('error', "Ops...something went wrong");
            }
        }else{
            Yii::$app->session->setFlash('success', "Pdf generato correttamente.<a href='/web/pdf/preventivi/".$filename."'>Scarica</a>");
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionSendEmailPayment($id_client, $id_quote){
        if(empty($id_client) || empty($id_quote)) return;
        
        $this->layout = "external-payment";
        print_r($id_client);
        print_r($id_quote);die;
        $client = Client::findOne(["id" => $id_client]);
        $order  = QuotePlaceholder::findOne(["id" => $id_quote]);
        
        if(empty($client) || empty($order)) return;
        
        if($this->sendEmail($model, $filename, "invio-ordine", $client->name." ".$client->surname.", ecco l'ordine delle tue bomboniere L'Uliveto", $client)){
            Yii::$app->session->setFlash('success', "Email inviata correttamente");
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [ORDER_SEND_EMAIL-100]");
        }

        return $this->redirect(["view", "id" => $id_quote]);
    }

    public function actionSearchForClientsFromSelect($q = ""){
        $term = $q;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => [], "status" => "100"];
        
        if (!is_null($term)) {
            $data = Client::find()
                        ->select(["id", "name", "surname"])
                        ->where(["LIKE", "surname", $term])
                        ->orWhere(["LIKE", "name", $term])
                        ->orderBy(["name" => SORT_ASC])
                        ->all();
                
            $i = 0;
            foreach($data as $client){
                $quote = Quote::findOne(["id_client" => $client->id]);
                if(!empty($quote)){
                    $out["results"][$i]["id"]   = $quote->id;
                    $out["results"][$i]["text"] = $quote->id."-".$client->name." ".$client->surname;
                    $i++;
                }
            }
            print_r($out);die;
            if(!empty($out["results"])){
                $out["status"] = "200";
            }
        }
        else if($id > 0){
            $out['results'] = ['id' => $id, 'text' => Client::find($id)->surname." ".Client::find($id)->name];
        }
        return $out;
    }

    protected function sendEmail($model, $filename, $view, $client){
        
        if(empty($model)) return false;
        
        $message = Yii::$app->mailer
                ->compose(
                    ['html' => $view],
                    ['model' => $model]
                )
                ->setFrom([Yii::$app->params["infoEmail"]])
                ->setTo($client->email)
                ->setSubject($object);

        $fullFilename = "https://manager.orcidelcilento.it/web/pdf/preventivi/".$filename;
        
        $message->attach($fullFilename);
        
        return $message->send();
    }
    
    public function actionGetTotal($id_quote){
        $out = ["status" => "100", "total" => 0];
        $quote = $this->findModel($id_quote);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $segnaposto = Segnaposto::findOne(["id" => $quote->id_placeholder]);
        $total = $quote->amount * floatval($segnaposto->price);
        
        $totaleWithVat = ($total + ($total / 100) * 22);
        
        return !empty($quote) ? $out = ["status" => "200", "total" => $totaleWithVat] : 0;
    }

    /**
     * Deletes an existing QuotePlaceholder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(["index"]);
    }

    /**
     * Finds the QuotePlaceholder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return QuotePlaceholder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = QuotePlaceholder::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
