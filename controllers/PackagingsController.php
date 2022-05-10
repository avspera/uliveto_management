<?php

namespace app\controllers;
use Yii;
use app\models\Packaging;
use app\models\PackagingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\utils\FKUploadUtils; 
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * PackagingsController implements the CRUD actions for Packaging model.
 */
class PackagingsController extends Controller
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
                            'index', 
                            'view', 
                            'update', 
                            'delete', 
                            'create', 
                            'get-by-id',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Packaging models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PackagingSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Packaging model.
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

    public function actionGetById($id){
        if(empty($id)) return;
        $out = ["status" => "100", "price" => 0];
        
        $packaging = Packaging::find()->select(["price"])->where(["id" => $id])->one();
        
        if(!empty($packaging)){
            $out["status"]  = "200";
            $out["price"]   = $packaging->price;
        }
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

    /**
     * Creates a new Packaging model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Packaging();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->created_at = date("Y-m-d H:i:s");
                if (!empty($_FILES)) {
                    $model->image = $this->manageUploadFiles($model);
                }
                if($model->save())
                    return $this->redirect(['view', 'id' => $model->id]);
            }else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong [PACK-100]");
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
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
        $path       = Yii::getAlias('@webroot')."/images/packaging/";
    
        $dirCreated = FileHelper::createDirectory($path);
        $image = UploadedFile::getInstance($model, 'image');
        
        if (!empty($image)){
            $filename = $uploader->generateAndSaveFile($image, $path);
            $model->image = "images/packaging/".$filename;
        }
        
        return $model->image;

    }

    /**
     * Updates an existing Packaging model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->created_at = date("Y-m-d");
            
            if (!empty($_FILES)) {
            
                $model->image = $this->manageUploadFiles($model);
            }
            if($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
            else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong [PACK-101]");
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Packaging model.
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
     * Finds the Packaging model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Packaging the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Packaging::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
