<?php

namespace app\controllers;
use Yii;
use app\models\Color;
use app\models\ColorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\utils\FKUploadUtils; 
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * ColorsController implements the CRUD actions for Color model.
 */
class ColorsController extends Controller
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
                            'delete-attachment'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Color models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ColorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDeleteAttachment($id = ""){
        if(Yii::$app->request->isAjax){
            
            if(empty($id)) return;

            $params     = Yii::$app->request->queryParams;
            
            if(!empty($params)){
                $color = $this->findModel($params["id"]);
                
                if(!empty($color->picture)){
                    $prevImage = $color->picture;
                    $color->picture = NULL;
                    if(!$color->save()){
                        Yii::$app->session->setFlash('error', "Ops...something went wrong [ORDER_DEL-100]");
                    }else{
                        try{
                            unlink(Yii::getAlias("@webroot")."/".$prevImage);
                            Yii::$app->session->setFlash('success', "Immagine cancellata correttamente");
                        }catch(yii\base\ErrorException $e){
                            Yii::$app->session->setFlash('error', "Ops...something went wrong [ORDER_DEL-101]");
                        }
                    }
                }
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $this->redirect(Yii::$app->request->referrer);;
        }
    }

    /**
     * Displays a single Color model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if($model = $this->findModel($id)){
            return $this->render('view', [
                'model' => $model
            ]);
        }else{
            Yii::$app->session->setFlash('error', "Ops...colore non trovato [COL-100]");
            return $this->redirect("index");
        }
    }

    /**
     * check each uploaded media in form.
     * if !empty, upload to server
     * path is: /images/blog/category/article_id
     */
    protected function manageUploadFiles($model) {

        $uploader   = new FKUploadUtils();
        $path       = Yii::getAlias('@webroot')."/images/colors/";
    
        $dirCreated = FileHelper::createDirectory($path);
        
        $picture = UploadedFile::getInstance($model, 'picture');
        if (!empty($picture)){
            $filename = $uploader->generateAndSaveFile($picture, $path);
            $model->picture = "images/colors/".$filename;
        }
        
        return $model->picture;
    }

    
    /**
     * Creates a new Color model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Color();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                
                if (!empty($_FILES)) {
                    $model->picture = $this->manageUploadFiles($model);
                }

                if($model->save()){
                    return $this->redirect(['view', 'id' => $model->id]);
                }else{
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [COL-104]");
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
     * Updates an existing Color model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $prevImage = $model->picture;

        if ($this->request->isPost && $model->load($this->request->post())) {
            
            if (!empty($_FILES)) {
                $model->picture = $this->manageUploadFiles($model);
            }

            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
            else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong [COL-102]");
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Color model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $color = $this->findModel($id);

        try{
            if(!empty($color->picture)){
                unlink(Yii::getAlias("@webroot")."/".$color->picture);
            }

            if($color->delete())
                Yii::$app->session->setFlash('success', "Colore cancellato con successo");

        }catch(yii\base\ErrorException $e){
            Yii::$app->session->setFlash('error', "Ops...something went wrong [ORDER_DEL-101]");
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the Color model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Color the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Color::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
