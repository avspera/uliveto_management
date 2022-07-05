<?php 
namespace app\controllers;

use Yii;
use app\models\Quote;
use app\models\QuotePlaceholder;
use app\models\Payment;
use app\models\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * this class is managed by cron in server.
 * It sends email in 2 cases
 * 1) For payments : 7gg e 5gg before
 * 2) Quote: 10gg e 5gg before deadline quote.
 */


class CronManagerController extends Controller
{

    public function actionStart(){
        
        $days = [7, 3, 2];
        $emails = 0;
        for($i = 0; $i < count($days); $i++){
            $emails += $this->sendReminders($days[$i], "payment");
            $emails += $this->sendReminders($days[$i], "payment_placeholder");
        }
        
        $days = [25, 15, 10, 5, 2];
        foreach($days as $key){
            //try to check from created_at and end of its month, because offerta ends on end of month
            $emails += $this->sendReminders($key, "scadenza_offerta");
        }
        
        return "Sent ".$emails." as reminders and ".$this->sendWishes()." as wishes";
    }

    private function sendWishes(){
        $today    = date("Y-m-d", mktime(null, null, null, date('m'), date('d'), date('Y')));
        
        $quotes = Quote::find()
                    ->where(["data_evento" => $today])
                    ->andWhere(["confirmed" => 1])
                    ->all();
        $sentEmails = 0;
        foreach($quotes as $quote){
            if($this->sendEmail([], $quote, "send-wishes", 0, "i nostri migliori auguri per il tuo lieto evento"))
                $sentEmails++;
        }
        
        return $sentEmails;
    }

    private function loopQuotes($quotes, $object, $day, $view, $flag = "quote"){
        $sentEmails = 0;
        foreach($quotes as $quote){
            if($quote->confirmed){

                if($flag == "quote_placeholder"){
                    $quote = Quote::findOne(["id_quote_placeholder" => $quote->id]);
                }

                $payments = Payment::findAll(["id_quote" => $quote->id]);
                
                if(count($payments) == 1){
                    $payment = new Payment();
                    $payment->created_at = date("Y-m-d H:i:s");
                    $payment->id_client = $quote->id_client;
                    $payment->fatturato = 0;
                    $payment->payed = 0;
                    $payment->payed = 1;
                    $payment->id_quote = $quote->id;
                    $payment->data_saldo = $quote->date_balance;
                    $payment->save();

                    if($this->sendEmail($payment, $quote, $view, $day, $object))
                        $sentEmails++;
                }
            }else{
                if($this->sendEmail([], $quote, $view, $day, $object))
                    $sentEmails++;
            }
            
        }

        return $sentEmails;
    }

    private function sendReminders($day, $flag){
        $latenza    = date("Y-m-d", mktime(null, null, null, date('m'), date('d') + $day, date('Y')));
        
        $sentEmails = 0;
        if($flag == "scadenza_offerta"){
            $quotes     = Quote::find()->where(["scadenza_offerta" => $latenza])->andWhere(["confirmed" => 0])->all();
            $sentEmails += $this->loopQuotes($quotes, "la tua offerta Bomboniere L'Uliveto è in scadenza", $day, "reminder-deadline-quote");    
        }
        
        if($flag == "payment"){
            $quotes     = Quote::find()->where(["date_balance" => $latenza])->andWhere(["confirmed" => 1])->all();
            $sentEmails += $this->loopQuotes($quotes, "ricordati di effettuare il pagamento per il tuo ordine Bomboniere L'Uliveto", $day, "reminder-payment");
        }

        if($flag == "payment_placeholder"){
            $quotes     = QuotePlaceholder::find()
                            ->leftJoin("quote", '`quote`.`id` = `quote_placeholder`.`id_quote`')
                            ->where(['`quote_placeholder`.`date_balance`' => $latenza])
                            ->andWhere(['`quote_placeholder`.`confirmed`' => 1])
                            ->all();
            $sentEmails += $this->loopQuotes($quotes, "ricordati di effettuare il pagamento per il tuo ordine Segnaposto L'Uliveto", $day, "reminder-payment", 'quote_placeholder');
        }
        
        return $sentEmails;
    }

    protected function sendEmail($payment, $order, $view, $days = 0, $object){
        
        $client = Client::find()->select(["name", "surname", "email"])->where(["id" => $order->id_client])->one();
        
        if(empty($client)) return false;
        
        $message = Yii::$app->mailer
                ->compose(
                    ['html' => $view],
                    ['payment' => $payment, "order" => $order, "days" => $days, "client" => $client]
                )
                ->setFrom([Yii::$app->params["infoEmail"] => Yii::$app->params["infoName"]])
                ->setTo($client->email)
                ->setSubject($client->name." ".$client->surname." ".$object);

        // $fullFilename = "https://manager.orcidelcilento.it/web/".$filename;
        // $message->attachContent($fullFilename,['fileName' => $filename,'contentType' => 'application/pdf']); 
        $sent = $message->send();
        
        if(!$sent){
            return "Unable to send Message";
        }else{
            return true;
        }
    }
}

?>