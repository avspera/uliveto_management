<?php

namespace app\controllers;
use Yii;
use app\models\Product;
use app\models\Packaging;
use app\models\Color;
use app\models\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\utils\FKUploadUtils; 
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
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
                            'get-info',
                            'error',
                            'get-colors',
                            'get-packaging'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Lists all Product models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if($model = $this->findModel($id)){
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [PROD-100]");
            return $this->redirect("index");
        }
        
    }

    public function actionGetInfo($id){

        if(empty($id)) return;
        $out = ["status" => "100", "price" => 0];
        
        $product = Product::find()->select(["price"])->where(["id" => $id])->one();
        if(!empty($product)){
            $out["status"]  = "200";
            $out["price"]   = $product->price;
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }
    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) ) {
                if (!empty($_FILES)) {
                    $model->image = $this->manageUploadFiles($model);
                }
                if($model->save())
                    return $this->redirect(['view', 'id' => $model->id]);
                else{
                    Yii::$app->session->setFlash('error', "Ops...something went wrong [PROD-101]");
                }
            }else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong [PROD-102]");
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionGetColors($id){
        $out = ["status" => "100", "msg" => "", "results" => []];
        
        $colors = Color::findAll(["id_product" => $id]);
        
        $i = 0;
        foreach($colors as $item){
            $out["results"][$i]["id"]   = $item->id;
            $out["results"][$i]["text"] = $item->label;
            $i++;
        }
        
        if(!empty($colors)) $out["status"] = "200";

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

    public function actionGetPackaging($id){
        $out = ["status" => "100", "msg" => "", "results" => []];
        
        $packaging = Packaging::findAll(["id_product" => $id]);
        
        $i = 0;
        foreach($packaging as $item){
            $out["results"][$i]["id"]   = $item->id;
            $out["results"][$i]["text"] = $item->label;
            $i++;
        }
        
        if(!empty($packaging)) $out["status"] = "200";

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

     /**
     * check each uploaded media in form.
     * if !empty, upload to server
     * path is: /images/blog/category/article_id
     */
    protected function manageUploadFiles($model) {

        $uploader   = new FKUploadUtils();
        $path       = Yii::getAlias('@webroot')."/images/prod/";
    
        $dirCreated = FileHelper::createDirectory($path);
        
        $image = UploadedFile::getInstance($model, 'image');
        if (!empty($image)){
            $filename = $uploader->generateAndSaveFile($image, $path);
            $model->image = "images/prod/".$filename;
        }
        
        return $model->image;

    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model      = $this->findModel($id);
        $prevImage  = $model->image;

        if ($this->request->isPost && $model->load($this->request->post()) ) {
            
            if (!empty($_FILES)) {
                $model->image = $this->manageUploadFiles($model);
            }
            
            if($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
            else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong [PROD-103]");
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $product = $this->findModel($id);
        try{
            
            if(!empty($product->picture)){
                unlink(Yii::getAlias("@webroot")."/".$product->image);
            }
            
            if($product->delete()){
                Yii::$app->session->setFlash('success', "Elemento cancellato con successo");
            }

        }catch(yii\base\ErrorException $e){
            Yii::$app->session->setFlash('error', "Ops...something went wrong [PROD_DEL-101]");
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
