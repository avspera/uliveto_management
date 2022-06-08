<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Quote;
use app\models\Payment;
use app\models\Client;
use app\models\Message;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
class SiteController extends Controller
{
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'request-password-reset', 
                            'reset-password',
                            'login',
                        ],
                        'allow' => true,
                        'allow' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'index'],
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
    

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction( $action ) {
        
        if ( parent::beforeAction ( $action ) ) {
    
            if ( $action->id == 'error' ) {
                $this->layout = 'error';
            }
            return true;
        } 
    }
    
    public function actionError()
{
    $exception = Yii::$app->errorHandler->exception;
    if ($exception !== null) {
        $this->layout = 'layout';
        return $this->render('error', ['exception' => $exception]);
    }
}


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest)
            return $this->redirect(["login"]);
            
        $quotesByMonth      = Quote::find()->select(["created_at", "confirmed"])->groupBy(["created_at", "confirmed"])->all();
        $quotesCount        = Quote::find()->where(["confirmed" => 0])->count();
        $quotesMonthCount   = [];
        $ordersMonthCount   = [];
        $months             = [ 1 => 0, 2 => 0, 3=> 0, 4=> 0, 5=> 0, 6=> 0, 7=> 0, 8=> 0, 9=> 0, 10=> 0, 11=> 0, 12=> 0];

        foreach($quotesByMonth as $quote) {
            $createdDateMonth = (int) date('m', strtotime($quote->created_at));
            
            if($quote->confirmed == 0){
                $quotesMonthCount[$createdDateMonth] += 1;
            }
            else{
                $ordersMonthCount[$createdDateMonth] += 1;
            }
        }
    
        $clientsCount   = Client::find()->count();
        $messagesCount  = Message::find()->where(["not", ["replied_at" => null]])->count();
        $ordersCount    = Quote::find()->where(["confirmed" => 1])->count();
        $paymentsCount  = Payment::find()->where(["payed" => 1])->count();

        $formattedMonthsQuotes = array_replace($months, $quotesMonthCount);
        $formattedMonthsOrders = array_replace($months, $ordersMonthCount);

        return $this->render('index', [
            "quotesCount"       => $quotesCount,
            'clientsCount'      => $clientsCount,
            'messagesCount'     => $messagesCount,
            'ordersCount'       => $ordersCount,
            'paymentsCount'     => $paymentsCount,
            'formattedMonthsQuotes'  => $formattedMonthsQuotes,
            'formattedMonthsOrders'  => $formattedMonthsOrders,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = "login";

        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // $auth       = \Yii::$app->authManager;
            // $userRole   = $auth->getRole(strtolower($model->getRole()));
            // $auth->assign($userRole, $user->getId());
            return $this->goHome();
        }

        $model->password = '';
        
        return $this->render('login', [
            'model' => $model,
            'error' => "Not a post request"
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect("login");
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

     /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = "request-password-reset";
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post())) {
            
            if($model->validate()){
                if ($model->sendEmail()) {
                    Yii::$app->session->setFlash('success', 'Ti abbiamo inviato un\'email con le istruzioni da seguire per reimpostare la password');
                } else {
                    Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
                }
            }else{
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
                $out = ["status" => "100", "message" => $errors["email"]];
            }
            
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $this->layout="resetPassword";
        
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate() && $model->resetPassword()){
                Yii::$app->session->setFlash('success', 'Password modificata correttamente');
                return $this->goHome();
            }else{
                Yii::$app->session->setFlash('error', 'Ops...c\'è stato qualche problema');
            }
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

}
