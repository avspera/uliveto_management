<?php 

/**
 * this class is managed by cron in server.
 * It sends email in 2 cases
 * 1) For payments : 7gg e 5gg before
 * 2) Quote: 10gg e 5gg before deadline quote.
 */

namespace app\utils;

use Yii;
use app\models\Quote;
use app\models\Payment;
use app\models\Client;

class GeneratePdf {

    function __construct(){
        parent::__construct();
        $this->runCron();
    }
    
    public function runCron(){

        $quotes = Quote::find()
                            ->select(["id"])
                            ->where(["<", "deadline", date("Y-m-d")])
                            ->andWhere([">", "deadline", date('Y-m-d', strtotime('+5 days'))])
                            ->all(); 

        foreach($quotes as $quote){
            if($quote->confirmed){
                $payments = Payment::findAll(["id_quote" => $id_quote]);
                if(count($payments) < 2){
                    $this->sendEmail($payment, $quote, "reminder-payment", 7);
                }
            }else{
                $this->sendEmail([], $quote, "reminder-deadline-quote", 7);
            }
        }

        $quotes = Quote::find()
                            ->select(["id"])
                            ->where(["data_evento" => date("Y-m-d")])
                            ->andWhere(["confirmed" => 1])
                            ->all(); 
        
        foreach($quotes as $quote){
            $this->sendEmail([], $quote, "send-wishes", 7);
        }
    }

    protected function sendEmail($payment, $order, $view, $days){
        
        if(empty($payment)) return false;
        $client = Client::find()->select(["email"])->where(["id" => $payment->id_client])->one();
        
        if(empty($client)) return false;

        $message = Yii::$app->mailer
                ->compose(
                    ['html' => $view],
                    ['payment' => $payment, "order" => $order, "days", $days, "client", $client]
                )
                ->setFrom([Yii::$app->params["infoEmail"]])
                ->setTo($client->email)
                ->setSubject($model->getClient()." ricorda di completare il pagamento per le tue bomboniere L'Uliveto");

        // $fullFilename = "https://manager.orcidelcilento.it/web/".$filename;
        // $message->attachContent($fullFilename,['fileName' => $filename,'contentType' => 'application/pdf']); 

        return $message->send();
    }
}

?>