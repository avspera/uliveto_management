<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\QuoteDetails;
use app\models\Product;
use app\models\Quote;
use app\models\Packaging;
/**
 * ProductController implements the CRUD actions for Product model.
 */
class QuoteDetailsController extends Controller
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
                    'delete' => ['POST', 'GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'delete',
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
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $flag = "order")
    {
        $model              = $this->findModel($id);
        $price              = Product::find()->select(["price"])->where(["id" => $model->id_product])->one();
        $quote              = Quote::findOne(["id" => $model->id_quote]);

        if(empty($quote)){
            Yii::$app->session->setFlash('error', "Ops...can't find quote [QUOTEDETAILS - 102]");
            return $this->redirect(['index']);
        }

        $currentTotalNoVat      = is_numeric($quote->total_no_vat) ? floatval($quote->total_no_vat) : 0;
        $subtractValue          = floatval($model->amount) * floatval($price->price);
        $newTotalNoVat          = $currentTotalNoVat - $subtractValue; 
        $newTotal               = ($newTotalNoVat + ($newTotalNoVat / 100) * 4);
        $quote->total_no_vat    = $newTotalNoVat;
        $quote->total           = $newTotal;

        if($model->delete() && $quote->save())
            Yii::$app->session->setFlash('success', "Elemento cancellato con successo");
        else
            Yii::$app->session->setFlash('error', "Ops...something went wront [QUOTEDETAILS - 101]");
        
        if($flag != "order")
            return $this->redirect(['quotes/update', "id" => $model->id_quote]);
        else
            return $this->redirect(['order/update', "id" => $model->id_quote]);
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
        if (($model = QuoteDetails::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
