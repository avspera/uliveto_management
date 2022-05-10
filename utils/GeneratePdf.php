<?php 

namespace app\utils;

use Yii;
use app\models\Quote;
use app\models\Product;
use app\models\Sales;
use app\models\Packaging;
use app\models\Segnaposto;
use app\models\QuotePlaceholder;
use app\models\QuoteDetails;
use app\models\Color;
use app\models\Client;
use kartik\mpdf\Pdf;
use Mpdf\Mpdf;
use \setasign\Fpdi\Fpdi;
use PHP_HTML;

class GeneratePdf {

    public static function quotePdf($quote, $flag, $file){
        
        if(empty($quote)) return;
        
        $quotePlaceholder = QuotePlaceholder::find()->where(["id_quote" => $quote->id])->one();
        $products   = QuoteDetails::findAll(["id_quote" => $quote->id]);
        $colors     = [];
        $sale       = Sales::findOne([$quote->id_sconto]);
        
        $client     = Client::findOne(["id" => $quote->id_client]);
        $confetti   = "NO";
        
        if($quote->confetti){
            if($quote->confetti_omaggio){
                $confetti = "OMAGGIO";
            }else{
                $confetti = number_format($quote->prezzo_confetti, 2, ",", ".") ." €";
            }
        }
    
        $balance = $quote->deposit ? $quote->total - $quote->deposit : $quote->total;

        ob_start();

        $pdf = new FPDI();
        
        // Reference the PDF you want to use (use relative path)
        $pagecount = $pdf->setSourceFile(Yii::getAlias("@webroot").'/pdf/'.$file.'.pdf');

        /**
         * PAGE ONE
         */
        // Import the first page from the PDF and add to dynamic PDF
        $tpl = $pdf->importPage(1);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->useTemplate($tpl);
        $pdf->SetFont('Helvetica');

        /**
         * HEADER
         */
        $pdf->setFontSize("10");
        //order number
        $pdf->SetXY(140, 13); // set the position of the box
        $pdf->Cell(0, 10, $quote->order_number, 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(139, 20); // set the position of the box
        $pdf->Cell(0, 10, $quote->formatDate($quote->created_at), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(132, 34); // set the position of the box
        $pdf->Cell(0, 10, $quote->getClient(), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(149, 41.5); // set the position of the box
        $pdf->Cell(0, 10, $client->phone, 0, 0, 'C'); // add the text, align to Center of cell
        /**
         * end of header
         */


        /**
         * PRODUCTS FOTO
         */
            $ordinate           = 80;
            $start_x            = 0;
            
            for($i= 0; $i <= count($products); $i++){
                $color  = Color::findOne(["id" => $products[$i]->id_color]);
                $item   = Product::find()->select(["name", "price"])->where(["id" => $products[$i]->id_product])->one(); 
                
                if($item->name == "[U]live")
                    $ordinate = 170;
                else if($item->name == "[U]gliarulo")
                    $ordinate = 125;
                else
                    $ordinate = 80;
                    
                if(!empty($color->picture)){
                    $pdf->Cell($pdf->Image($color->picture,$start_x, $ordinate, 40, 40));
                    $start_x += 45;
                }
                
                if($i > 0){
                    $currentProd = $products[$i]->id_product;
                    $prevProd    = $products[$i-1]->id_product;
                    if($currentProd !== $prevProd){
                        $start_x = 0;
                    }
                }

                $packaging = Packaging::find()->select(["image"])->where(["id_product" => $item->id])->one();
                if(!empty($packaging)){
                    $ordinate = 220;
                    $pdf->Cell($pdf->Image($packaging->image, $start_x, $ordinate, 40, 40));
                }
            }

            /**
             * CONFETTI.
             */
            if($confetti == "OMAGGIO"){
                $pdf->setXY(185, 240.5);
                $pdf->setTextColor(0, 177, 106);
            }else{
                $pdf->setXY(180, 240.5);
            }

            $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $confetti), 0, 0, 'C'); // add the text, align to Center of cell
            /**
             * end of CONFETTI.
             */
        
              /**
             * con fiocco SEMPRE SI
             */
            $pdf->setXY(165, 232.5);
            $pdf->setTextColor(0, 0, 0);
            $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", "SI"), 0, 0, 'C'); // add the text, align to Center of cell
            
        
        
        /**
         *  PAGE 2
         */
        $tpl = $pdf->importPage(2);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->useTemplate($tpl);
        $pdf->setFont("Helvetica");
        $pdf->setFontSize("14");
        $pdf->setTextColor(0, 0, 0);
        /**
         * HEADER
         */
        $pdf->setFontSize("10");
        //order number
        $pdf->SetXY(140, 13); // set the position of the box
        $pdf->Cell(0, 10, $quote->order_number, 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(139, 20); // set the position of the box
        $pdf->Cell(0, 10, $quote->formatDate($quote->created_at), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(132, 34); // set the position of the box
        $pdf->Cell(0, 10, $quote->getClient(), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(149, 41.5); // set the position of the box
        $pdf->Cell(0, 10, $client->phone, 0, 0, 'C'); // add the text, align to Center of cell
        /**
         * end of header
         */

         
        //RIEPILOGO ORDINE BOX
        $line = 82;
        foreach($products as $product){
            $item = Product::findOne(["id" => $product->id_product]);
            //summary
            $pdf->setXY(30, $line);
            $pdf->setFontSize("11");
            $pdf->setTextColor(0, 0, 0);
            $pdf->Cell(10, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $item->name." - ".number_format($item->price, 2, ",", ".") ." €")." | n. ".$product->amount, 0, 0, 'C'); // add the text, align to Center of cell
            $line += 7;

            //prezzo scontato
            if(!empty($sale)){
                $prezzoScontato = 0;
                $percentage      = floatval($item->price*$sale->amount/100);
                $prezzoScontato  = $item->price - $percentage;
            
                $pdf->setTextColor(0, 177, 106);
                $pdf->setXY(45, $line-2);
                $pdf->Cell(10, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", "Sconto: ".$sale->name." del ".$sale->amount."% - ".number_format($prezzoScontato, 2, ",", ".") ." €")." n. ".$product->amount, 0, 0, 'C'); // add the text, align to Center of cell
                $line += 10;
            }else{
                $line += 6;
            }

            //add prezzo packaging + prezzo confetti
            $subtotal = $prezzoScontato; 
            $packagingPrice = !empty($packaging) ? floatval($packaging->price) : 0;
            $confettiPrice  = $quote->confetti_omaggio ? 0 : floatval($quote->prezzo_confetti);
            $customPrice    = !empty($quote->custom_amount) ? floatVal($quote->custom_amount) : 0;
            $totalPrice = $subtotal + $packagingPrice + $confettiPrice + $customPrice;
        
            $line += 1;
            
            $pdf->setTextColor(0, 0, 0);
            $pdf->setXY(8, $line-9);
                $pdf->Cell(10, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", 
                "Totale: ".number_format($totalPrice, 2, ",", ".") ." €"
            ));
        }

        //END OF FOREACH
            
        /**
         * RIEPILOGO ORDINE
         */
            $pdf->setTextColor(0, 0, 0);
            $pdf->setFontSize("10");

            //total
            $pdf->setXY(32, 192);
            $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", number_format($quote->total, 2, ",", ".")." €"), 0, 0, 'C');

            //deposit
            
            $pdf->setXY(37, 200);
            $pdf->Cell(56, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->deposit  ? number_format($quote->deposit, 2, ",", ".") ." € - ".$quote->formatDate($quote->date_deposit) : ""), 0, 0, 'C');

            //saldo
            $pdf->setXY(33, 208);
            $pdf->Cell(52, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT",  number_format($balance, 2, ",", ".") ." € - ".$quote->formatDate($quote->date_balance)), 0, 0, 'C');

            //shipping
            $pdf->setXY(21, 216);
            $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->shipping ? "SI" : "NO"), 0, 0, 'C');

            if($quote->address){
                $pdf->setXY(27, 230);
                $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->address), 0, 0, 'C');
            }

            $pdf->setXY(44, 239);
            $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->formatDate($quote->deadline)), 0, 0, 'C');

            $pdf->setXY(90, 210);
            $pdf->Cell(0, 20, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->custom." ".empty($quote->custom_amount) ? "" : number_format($quote->custom_amount, 2, ",", ".") ." €"), 0, 0, 'C');
            
        /**
         * 
         */

        /**
        * PLACEHOLDER INFO
        */
        if($quotePlaceholder){

            $placeholder = Segnaposto::find()->select(["image"])->where(["id" => $quotePlaceholder->id_placeholder])->one();

            if(!empty($placeholder->image)){
                $pdf->Cell($pdf->Image($placeholder->image,0, 0, 40, 40));
            }
            

            $pdf->setFontSize("11");
            $pdf->setXY(90, 131);
            $pdf->Cell(0, 10, $quotePlaceholder ? "SI n.".$quotePlaceholder->amount : "NO", 0, 0, 'C'); // add the text, align to Center of cell

            $pdf->setXY(95, 143.5);
            $pdf->Cell(0, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", number_format($quotePlaceholder->total, 2, ",", ".")." €"), 0, 0, 'C');

            //shipping
            $pdf->setXY(120, 166);
            $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->shipping ? "SI" : "NO"), 0, 0, 'C');

            if($quote->address){
                $pdf->setXY(127, 181);
                $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->address), 0, 0, 'C');
            }
            
            $pdf->setXY(145, 190.5);
            $pdf->Cell(30, 10, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->formatDate($quote->deadline)), 0, 0, 'C');
        }
        
       

        $tpl = $pdf->importPage(3);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->useTemplate($tpl);
        $pdf->setFont("Helvetica");
        $pdf->setFontSize("14");

        //client info header
        $pdf->setFontSize("10");
        //order number
        $pdf->SetXY(140, 13); // set the position of the box
        $pdf->Cell(0, 10, $quote->order_number, 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(139, 20); // set the position of the box
        $pdf->Cell(0, 10, $quote->formatDate($quote->created_at), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(132, 34); // set the position of the box
        $pdf->Cell(0, 10, $quote->getClient(), 0, 0, 'C'); // add the text, align to Center of cell

        $pdf->SetXY(149, 41.5); // set the position of the box
        $pdf->Cell(0, 10, $client->phone, 0, 0, 'C'); // add the text, align to Center of cell

        //LUOGO E DATA
        $pdf->setFontSize("12");
        $pdf->setXY(23, 190);
        $pdf->Cell(30, 0, iconv('UTF-8', "ISO-8859-1//TRANSLIT", "Trentinara, ".$quote->formatDate($quote->created_at)), 0, 0, 'C');
        
        $pdf->setXY(0, 215);
        $pdf->Cell(0, 0, iconv('UTF-8', "ISO-8859-1//TRANSLIT", $quote->notes), 0, 0, 'C');

        $filename = $file."_".$quote->order_number."_".$client->name."_".$client->surname.".pdf";
        ob_get_clean();

        // $pdf->Output();die; //If test
        
        $pdf->Output($filename, $flag == "send" ? 'F' : 'D');    

        return $filename;
    }
}
?>

