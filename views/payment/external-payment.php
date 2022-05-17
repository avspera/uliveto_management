<?php 
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\DetailView;
    use yii\widgets\ActiveForm;
    use kartik\file\FileInput;
    
    $this->title = "";

    /**
     * Capture result Object {
  "id": "52G24664WC8288430",
  "intent": "CAPTURE",
  "status": "COMPLETED",
  "purchase_units": [
    {
      "reference_id": "default",
      "amount": {
        "currency_code": "EUR",
        "value": "573.30"
      },
      "payee": {
        "email_address": "sb-qow5b3775977@personal.example.com",
        "merchant_id": "JY56Q856AL3D4"
      },
      "shipping": {
        "name": {
          "full_name": "John Doe"
        },
        "address": {
          "address_line_1": "Via Unit? d'Italia",
          "admin_area_2": "Napoli",
          "admin_area_1": "Napoli",
          "postal_code": "80127",
          "country_code": "IT"
        }
      },
      "payments": {
        "captures": [
          {
            "id": "4P3959445H566582K",
            "status": "COMPLETED",
            "amount": {
              "currency_code": "EUR",
              "value": "573.30"
            },
            "final_capture": true,
            "seller_protection": {
              "status": "ELIGIBLE",
              "dispute_categories": [
                "ITEM_NOT_RECEIVED",
                "UNAUTHORIZED_TRANSACTION"
              ]
            },
            "create_time": "2022-04-13T09:21:08Z",
            "update_time": "2022-04-13T09:21:08Z"
          }
        ]
      }
    }
  ],
  "payer": {
    "name": {
      "given_name": "John",
      "surname": "Doe"
    },
    "email_address": "sb-ll1xm15286877@personal.example.com",
    "payer_id": "GF8MT56D6GNN2",
    "address": {
      "country_code": "IT"
    }
  },
  "create_time": "2022-04-13T09:19:31Z",
  "update_time": "2022-04-13T09:21:08Z",
  "links": [
    {
      "href": "https://api.sandbox.paypal.com/v2/checkout/orders/52G24664WC8288430",
      "rel": "self",
      "method": "GET"
    }
  ]
}
     * 
     */
?> 
    <style>
        .blocks .btn-success 
        {
            margin: 0 5px;
        },

        .btn-group{
            margin: 5px 0;
        }
    </style>
    <div class="card" style="width: 550px; margin: 0 auto;float: none; margin-top: 20px">
        <div class="card-body table-responsive login-card-body">
          <?php if(Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-warning alert-dismissible" style="color: white">
              <?php echo Yii::$app->session->getFlash('error'); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?> 

          <?php if(Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible">
              <?php echo Yii::$app->session->getFlash('success'); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?> 

            <p>Ciao, <?= $client->name." ".$client->surname ?></p>

            <p>Qui di seguito puoi effettuare il pagamento per il tuo ordine <strong>#<?= isset($quote->order_number) ? $quote->order_number : $quote->id ?> del <?= $quote->formatDate($quote->created_at) ?></strong></p>

                <div class="card">
                    <div id="collapseTwo" class="show" aria-labelledby="headingTwo" data-parent="#accordion">
                        <div class="card-body">
                            <div class="text-lg">Tipo: <?= $payment->getType(); ?>. Totale: <?= $quote->formatNumber($payment->amount) ?></div>
                            <div class="btn-group blocks" style="margin-top:10px" data-toggle="buttons">
                                <label class="btn btn-success btn-lg active">
                                    <input type="radio" amount="<?= $payment->amount ?>" name="options" id="percentage_100" autocomplete="off"> <?= $payment->amount ?>
                                </label>
                            </div>
                            <div style="margin-top: 10px">
                                <div id="paypal-button-container"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 10px">
                  <p>Oppure carica la ricevuta se hai già provveduto al pagamento</p>
                  <?php 
                    $form = ActiveForm::begin([
                        'action' => ['upload-allegato'],
                        'method' => 'post',
                        'options' => ['enctype' => 'multipart/form-data'],
                    ]);
                  ?>
                    <?php echo $form->field($payment, 'id')->hiddenInput(['value'=> $payment->id])->label(false); ?>
                    <?= FileInput::widget([
                        'model' => $payment,
                        'attribute' => 'allegato',
                        'options' => ['multiple' => true, 'accept' => ["png", "jpg", "pdf"]]
                    ]);?>
                    <div class="form-group">
                        <?= Html::submitButton('Carica', ['class' => 'btn btn-success', 'style' => "margin-top: 5px"]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <!-- /.login-card-body -->
    </div>

    <script>
      paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: (data, actions) => {
          return actions.order.create({
            purchase_units: [{
              amount: {
                value: getAmount() // Can also reference a variable or function
              }
            }]
          });
        },
        // Finalize the transaction after payer approval
        onApprove: (data, actions) => {
          return actions.order.capture().then(function(orderData) {
            const transaction = orderData.purchase_units[0].payments.captures[0];
            registerTransaction(transaction);
            // When ready to go live, remove the alert and show a success message within this page. For example:
            // const element = document.getElementById('paypal-button-container');
            // element.innerHTML = '<h3>Thank you for your payment!</h3>';
            // Or go to another URL:  actions.redirect('thank_you.html');
          });
        }
      }).render('#paypal-button-container');

    function getAmount(){
        let amount = $('input[name="options"]:checked').attr("amount");
        console.log("amount", amount);
        return amount
    }

    function registerTransaction(transaction){
        $.ajax({
            url: '<?= Url::to(['payment/register-transaction']) ?>',
            type: 'get',
            dataType: 'json',
            'data': {
                'transaction': JSON.stringify(transaction),
                'id_quote': <?= $quote->id ?>,
                'id_client': <?= $client->id ?>
            },
            success: function (data) {
                let alertClass = "alert-warning";
                let alertMsg = "Ops...c'è stato qualche problema. Riprova."

                if(data.status == "200"){
                    alertClass  = "alert-success";
                    alertMsg    = data.msg
                }

                let html = `<div style="margin-top: 5px;" class="alert ${alertClass} alert-dismissible">
                    ${alertMsg} . <br />
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>`;

                $(".error-content").append(html);
            },
            error: function(error){
                alert("Ops...c'è stato un problema tecnico. Riprova")
            }
        });
    }
    </script>