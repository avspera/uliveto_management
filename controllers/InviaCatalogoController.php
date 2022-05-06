<?php

namespace app\controllers;

use Yii;
use app\models\InviaCatalogo;
use app\models\InviaCatalogoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\utils\FKUploadUtils; 
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
/**
 * InviaCatalogoController implements the CRUD actions for InviaCatalogo model.
 */
class InviaCatalogoController extends Controller
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
                            'upload-files',
                            'view-catalogs',
                            'delete-catalog',
                            'send-catalog'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Lists all InviaCatalogo models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new InviaCatalogoSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->sort->defaultOrder = ["created_at" => SORT_DESC];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InviaCatalogo model.
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


    public function actionViewCatalogs(){
        $existingFiles = [];

        try{
            $existingFiles=\yii\helpers\FileHelper::findFiles(Yii::getAlias("@webroot").'/pdf', ['recursive' => false] );
        }catch(InvalidArgumentException $e){
            throw new InvalidArgumentException("Ops...we got a problem [VIEW_CATS_01]");
        }

        return $this->render('view-catalogs', [
            'existingFiles' => $existingFiles
        ]);
    }

    public function actionSendCatalog($id, $flag){
        
        $model = $this->findModel($id);
        
        if($flag == "email"){
            $this->sendEmail($model);
            Yii::$app->session->setFlash('success', "Email inviata correttamente");
        }   
        
        return $this->redirect(["view", "id" => $id]);
    }

    /**
     * Creates a new InviaCatalogo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new InviaCatalogo();

        if ($this->request->isPost) {
            
            if ($model->load($this->request->post())) {
                $model->created_at = date("Y-m-d H:i:s");
                if($model->save()){
                    Yii::$app->session->setFlash('success', "Catalogo inviato correttamente");
                    return $this->redirect(['view', 'id' => $model->id]);
                }else{
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [CAT-101]");
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionDeleteCatalog($name){
        if(unlink($name)){
            return $this->redirect(Yii::$app->request->referrer);
        }else{
            return $this->redirect("view-catalogs");
        }
    }


    public function actionUploadFiles(){
        
        $model = new InviaCatalogo();

        if ($model->load($this->request->post()) ) {
            if (!empty($_FILES)) {
                try{
                    $existingFiles=\yii\helpers\FileHelper::findFiles(Yii::getAlias("@webroot").'/pdf', ['recursive' => false] );
                    //delete existing files if there are new to update
                    foreach($_FILES["InviaCatalogo"]["name"]["files"] as $file) {
                        if (strpos($file, "catalogo") !== FALSE)
                            unlink(Yii::getAlias("@webroot").'/pdf/'.$file);
                        
                        if (strpos($file, "prezzario") !== FALSE)
                            unlink(Yii::getAlias("@webroot").'/pdf/'.$file);
                    }
                }catch(InvalidArgumentException $e){
                    throw new InvalidArgumentException("Ops...we got a problem [INVIA_CAT_01]");
                }

                if($model->files = $this->manageUploadFiles($model))
                    Yii::$app->session->setFlash('success', "Files caricati correttamente");
                else{
                    print_r($model->getErrors());die;
                }
                return $this->redirect(['index']);
            }   
        }else
        
        return $this->render('_form_catalogo', [
            'model' => $model,
        ]);
    }

    /**
     * check each uploaded media in form.
     * if !empty, upload to server
     * path is: /images/blog/category/article_id
     */
    protected function manageUploadFiles($model) {

        $uploader   = new FKUploadUtils();
        $path       = Yii::getAlias('@webroot')."/pdf/";
        
        $dirCreated = FileHelper::createDirectory($path);
        $files      = UploadedFile::getInstances($model, 'files');
        if (!empty($files)){
            foreach($files as $file){
                if($uploader->generateAndSaveFile($file, $path))
                    return true;
            }
            
        }
        
        return false;
    }

    /**
     * Updates an existing InviaCatalogo model.
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
            Yii::$app->session->setFlash('error', "Ops...something went wrong [CAT-102]");
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing InviaCatalogo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', "Catalogo cancellato con successo");
        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function sendEmail($model){
        if(empty($model)) return false;
        
        $existingFiles=\yii\helpers\FileHelper::findFiles(Yii::getAlias("@webroot").'/pdf/', ['recursive' => false] );
        
        $files = [];
        $i = 0;

        $message = Yii::$app->mailer
                ->compose(
                    ['html' => 'invio-catalogo'],
                    ['model' => $model]
                )
                ->setFrom([Yii::$app->params["infoEmail"]])
                ->setTo($model->email)
                ->setSubject($model->name." ecco i nostri cataloghi");

        foreach($existingFiles as $file) {
            $file = substr($file, strpos($file, "/home/")+strlen("/home/"));
            $filename = "https://manager.orcidelcilento.it/".$file;
            $message->attach($filename);
        }
        
        return $message->send();
    }

    /**
     * Finds the InviaCatalogo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return InviaCatalogo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InviaCatalogo::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
