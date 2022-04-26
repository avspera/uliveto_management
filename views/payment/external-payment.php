<?php 
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\DetailView;
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
    <div class="card" style="width: 500px; margin: 0 auto;float: none; margin-top: 20px">
        <div class="card-body table-responsive login-card-body">
            <div class="error-content" style="margin-left: auto;">
                
            </div>

            <p>Ciao, <?= $client->name." ".$client->surname ?></p>

            <p>Qui di seguito puoi effettuare il pagamento per il tuo ordine <strong>#<?= $quote->order_number ?> del <?= $quote->formatDate($quote->created_at) ?></strong></p>

                <div class="card">
                    <div id="collapseTwo" class="show" aria-labelledby="headingTwo" data-parent="#accordion">
                        <div class="card-body">
                            <div class="text-lg">Totale: <?= $quote->formatNumber($quote->total) ?></div>
                            <div class="text-md">Scegli la quota</div>

                            <?php if($hasAcconto) { ?>
                                <div class="btn-group blocks" style="margin-top:10px" data-toggle="buttons">
                                    <label class="btn btn-success btn-lg active">
                                        <input type="radio" amount="<?= $quote->calculatePercentage(100, $quote->total) ?>" name="options" id="percentage_100" autocomplete="off"> <?= $quote->calculatePercentage(100, $quote->total, true) ?>
                                    </label>
                                </div>
                            <?php } else { ?>
                                <div class="btn-group blocks" style="margin-top:10px" data-toggle="buttons">
                                    <label class="btn btn-success active">
                                        <input type="radio" amount="<?= $quote->calculatePercentage(20, $quote->total) ?>" name="options" id="percentage_20" autocomplete="off"> 20% = <?= $quote->calculatePercentage(20, $quote->total, true) ?>
                                    </label>
                                    <label class="btn btn-success">
                                        <input type="radio" amount="<?= $quote->calculatePercentage(30, $quote->total) ?>" name="options" id="percentage_30" autocomplete="off"> 30% = <?= $quote->calculatePercentage(30, $quote->total, true) ?>
                                    </label>
                                    <label class="btn btn-success">
                                        <input type="radio" amount="<?= $quote->calculatePercentage(40, $quote->total) ?>" name="options" id="percentage_40" autocomplete="off"> 40% = <?= $quote->calculatePercentage(40, $quote->total, true) ?>
                                    </label>
                                </div>
                            <?php } ?>
                            <div style="margin-top: 10px">
                                <div id="paypal-button-container"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Dettaglio Ordine
                        </button>
                    </h5>
                    </div>

                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#headingOne">
                        <div class="card-body">
                            <?= DetailView::widget([
                                'model' => $quote,
                                'attributes' => [
                                    'order_number',
                                    [
                                        'attribute' => 'created_at',
                                        'value' => function($model){
                                            return $model->formatDate($model->created_at, true);
                                        }
                                    ],
                                    [
                                        'attribute' => 'updated_at',
                                        'value' => function($model){
                                            return $model->formatDate($model->updated_at, true);
                                        }
                                    ],
                                    [
                                        'attribute' => 'id_client',
                                        'value' => function($model){
                                            return Html::a($model->getClient(), Url::to(["clients/view", "id" => $model->id_client]));
                                        },
                                        'format' => "raw"
                                    ],
                                    [
                                        'attribute' => 'placeholder',
                                        'value' => function($model){
                                            return $model->getPlaceholder();
                                        },
                                        'format' => "raw"
                                    ],
                                    [
                                        'attribute' => "confetti",
                                        'value' => function($model){
                                            $html = "";
                                            if($model->confetti){
                                                if($model->confetti_omaggio){
                                                    $html .= "<span style='text-decoration: line-through;'>".$model->formatNumber($model->prezzo_confetti)."</span> <span style='color: green'> - in omaggio</span>";
                                                }
                                                else{
                                                    $html .= "<span>".$model->formatNumber($model->prezzo_confetti)."</span>";
                                                }
                                            }else{
                                                $html .= "-";
                                            }
                                            return $html;
                                        },
                                        'format' => "raw"
                                    ],
                                    [
                                        'attribute' => "custom_amount",
                                        'value' => function($model){
                                            return !empty($model->custom_amount) ? $model->formatNumber($model->custom_amount) : "-";
                                        }
                                    ],
                                    'custom:ntext',
                                    'notes:ntext',
                                    [
                                        'attribute' => 'total_no_vat',
                                        'value' => function($model){
                                            return $model->formatNumber($model->total_no_vat);
                                        },
                                        'format' => "raw"
                                    ],
                                    [
                                        'attribute' => 'total',
                                        'value' => function($model){
                                            return $model->formatNumber($model->total);
                                        },
                                        'format' => "raw"
                                    ],
                                    [
                                        'attribute' => 'id_sconto',
                                        'value' => function($model){
                                            return $model->getSale($model->id_sconto);
                                        },
                                        'format' => "raw"
                                    ],
                                    [
                                        'attribute' => 'deposit',
                                        'value' => function($model){
                                            return $model->formatNumber($model->deposit);
                                        },
                                        'format' => "raw"
                                    ],
                                    [
                                        'attribute' => 'balance',
                                        'value' => function($model){
                                            return $model->formatNumber($model->balance);
                                        },
                                        'format' => "raw"
                                    ],
                                    [
                                        'attribute' => 'shipping',
                                        'value' => function($model){
                                            return $model->shipping ? $model->address : "NO";
                                        }
                                    ],
                                    [
                                        'attribute' => 'deadline',
                                        'value' => function($model){
                                            return $model->formatDate($model->deadline);
                                        },
                                        'format' => "raw"
                                    ],
                                    [
                                        'attribute' => "confirmed",
                                        "value" => function($model){
                                            return $model->confirmed ? "SI" : "NO";
                                        }
                                    ],
                                    [
                                        'attribute' => "attachments",
                                        'value' => function($model){
                                            if(!empty($model->attachments)){
                                                $html = "<div class='row'>";
                                                $attachments = json_decode($model->attachments, true);
                                                foreach($attachments as $file){
                                                    $html .= "<div style='margin: 5px'>";
                                                    $html .= Html::a("<i class='fas fa-file'></i> Allegato", Url::to([$file]));
                                                    $html .= "</div>";
                                                }
                                                $html .= "</div>";

                                                return $html;
                                            }else{
                                                return "-";
                                            }
                                        },
                                        'format' => "raw"
                                    ]
                                ],
                            ]) ?>
                        </div>
                    </div>
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