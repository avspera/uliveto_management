<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index', 
                            'create', 
                            'view', 
                            'update', 
                            'delete',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [
                            'check-email'
                        ],
                        'allow' => true,
                        'allow' => ['?'],
                    ],
                ],
            ],
        ];
    }


    protected function manageUploadFiles($model) {

        $uploader   = new FKUploadUtils();
        $path       = Yii::getAlias('@webroot')."/images/users/";
    
        $dirCreated = FileHelper::createDirectory($path);
        
        $image = UploadedFile::getInstance($model, 'picture');
        if (!empty($image)){
            $filename = $uploader->generateAndSaveFile($image, $path);
            $model->picture = "images/users/".$filename;
        }
        
        return $model->picture;

    }

    public function actionCheckEmail($email){
        if(empty($email)) return;
        $out = ["status" => "100"];
        
        $client = User::findOne(["email" => $email]);
        if(!empty($client)){
            $out["status"]  = "200";
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(!Yii::$app->user->identity->isAdmin()){
            return $this->goHome();
        }
        
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if($model = $this->findModel($id)){
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }else{
            Yii::$app->session->setFlash('error', "Ops...something went wrong [PROD-101]");
            return $this->redirect("index");
        }
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            $model->created     = date("Y-m-d H:i:s");
            $model->last_login  = date("Y-m-d H:i:s");
            $model->status      = User::STATUS_ACTIVE;
            $model->setPassword($model->password);
            
            if (!empty($_FILES)) {
                $model->picture = $this->manageUploadFiles($model);
            }

            if($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
            else{
                Yii::$app->session->setFlash('error', "Ops...something went wrong [USER-100]");
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if($model->new_password != "" && ($model->new_password == $model->new_password_confirm)){
                $model->setPassword($model->new_password);
            }

            if (!empty($_FILES)) {
                $model->picture = $this->manageUploadFiles($model);
            }

            if($model->save()){
                Yii::$app->session->setFlash('success','Aggiornamento avvenuto con successo');
            }
            else{
                Yii::$app->session->setFlash('success','Ops...qualcosa è andato storto');
            }
            return $this->redirect(['view', 'id' => $model->id]);
            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', "Elemento cancellato con successo");
        return $this->redirect(["index"]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
