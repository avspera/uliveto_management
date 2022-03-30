<?php 
  use yii\helpers\Html;
  use yii\helpers\Url;
?> 
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->

    <link href="https://fonts.googleapis.com/css?family=Work+Sans:200,300,400,500,600,700" rel="stylesheet">

    <!-- CSS Reset : BEGIN -->
    <style>

        html,
        body {
                margin: 0 auto !important;
                padding: 0 !important;
                height: 100% !important;
                width: 100% !important;
                background: #f1f1f1;
            }

        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        /* What it does: Centers email on Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        /* What it does: Fixes webkit padding issue. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        /* What it does: Uses a better rendering method when resizing images in IE. */
        img {
            -ms-interpolation-mode:bicubic;
        }

        /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
        a {
            text-decoration: none;
        }

        /* What it does: A work-around for email clients meddling in triggered links. */
        *[x-apple-data-detectors],  /* iOS */
        .unstyle-auto-detected-links *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }

        /* What it does: Prevents Gmail from changing the text color in conversation threads. */
        .im {
            color: inherit !important;
        }

        /* If the above doesn't work, add a .g-img class to any image in question. */
        img.g-img + div {
            display: none !important;
        }

        /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
        /* Create one of these media queries for each additional viewport size you'd like to fix */

        /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            u ~ div .email-container {
                min-width: 320px !important;
            }
        }
        /* iPhone 6, 6S, 7, 8, and X */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            u ~ div .email-container {
                min-width: 375px !important;
            }
        }
        /* iPhone 6+, 7+, and 8+ */
        @media only screen and (min-device-width: 414px) {
            u ~ div .email-container {
                min-width: 414px !important;
            }
        }

    </style>

    <!-- CSS Reset : END -->

    <!-- Progressive Enhancements : BEGIN -->
    <style>

	    .primary{
	background: #2f89fc;
        }
        .bg_white{
            background: #ffffff;
        }
        .bg_light{
            background: #fafafa;
        }
        .bg_black{
            background: #000000;
        }
        .bg_dark{
            background: rgba(0,0,0,.8);
        }
        .email-section{
            padding:2.5em;
        }

        /*BUTTON*/
        .btn{
            padding: 5px 15px;
            display: inline-block;
        }
        .btn.btn-primary{
            border-radius: 5px;
            background: #2f89fc;
            color: #ffffff;
        }
        .btn.btn-white{
            border-radius: 5px;
            background: #ffffff;
            color: #000000;
        }
        .btn.btn-white-outline{
            border-radius: 5px;
            background: transparent;
            border: 1px solid #fff;
            color: #fff;
        }

        h1,h2,h3,h4,h5,h6{
            font-family: 'Work Sans', sans-serif;
            color: #000000;
            margin-top: 0;
            font-weight: 400;
        }

        body{
            font-family: 'Work Sans', sans-serif;
            font-weight: 400;
            font-size: 15px;
            line-height: 1.8;
            color: rgba(0,0,0,.4);
        }

        a{
            color: #2f89fc;
        }

        table{
        }
        /*LOGO*/

        .logo h1{
            margin: 0;
        }
        .logo h1 a{
            color: #000000;
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            font-family: 'Poppins', sans-serif;
        }

        .navigation{
            padding: 0;
        }
        .navigation li{
            list-style: none;
            display: inline-block;;
            margin-left: 5px;
            font-size: 13px;
            font-weight: 500;
        }
        .navigation li a{
            color: rgba(0,0,0,.4);
        }

        /*HERO*/
        .hero{
            position: relative;
            z-index: 0;
        }

        .hero .text{
            color: rgba(0,0,0,.3);
        }
        .hero .text h2{
            color: #000;
            font-size: 30px;
            margin-bottom: 0;
            font-weight: 300;
        }
        .hero .text h2 span{
            font-weight: 600;
            color: #2f89fc;
        }


        /*HEADING SECTION*/
        .heading-section{
        }
        .heading-section h2{
            color: #000000;
            font-size: 28px;
            margin-top: 0;
            line-height: 1.4;
            font-weight: 400;
        }
        .heading-section .subheading{
            margin-bottom: 20px !important;
            display: inline-block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(0,0,0,.4);
            position: relative;
        }
        .heading-section .subheading::after{
            position: absolute;
            left: 0;
            right: 0;
            bottom: -10px;
            content: '';
            width: 100%;
            height: 2px;
            background: #2f89fc;
            margin: 0 auto;
        }

        .heading-section-white{
            color: rgba(255,255,255,.8);
        }
        .heading-section-white h2{
            font-family: 
            line-height: 1;
            padding-bottom: 0;
        }
        .heading-section-white h2{
            color: #ffffff;
        }
        .heading-section-white .subheading{
            margin-bottom: 0;
            display: inline-block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255,255,255,.4);
        }

        /*PROJECT*/
        .text-project{
            padding-top: 10px;
        }
        .text-project h3{
            margin-bottom: 0;
        }
        .text-project h3 a{
            color: #000;
        }

        /*FOOTER*/

        .footer{
            color: "#336633"
        }

        .footer .heading{
            color: #ffffff;
            font-size: 20px;
        }
        .footer ul{
            margin: 0;
            padding: 0;
        }
        .footer ul li{
            list-style: none;
            margin-bottom: 10px;
        }
        .footer ul li a{
            color: white;
        }


        @media screen and (max-width: 500px) {


        }


    </style>


</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #222222;">
	<center style="width: 100%; background-color: #f1f1f1;">
    <div style="max-width: 600px; margin: 0 auto;" class="email-container">
    	<!-- BEGIN BODY -->
      <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
      	<tr>
          <td valign="top" class="bg_white" style="padding: 1em 2.5em;">
          	<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
          		<tr>
          			<td class="logo" style="text-align: center;">
			            <h1><a href="#"><?= Html::img(Url::to("https://manager.orcidelcilento.it/web/images/logo.png"), ["style" => "width: 300px"]) ?></a></h1>
			          </td>
          		</tr>
          	</table>
          </td>
	      </tr><!-- end tr -->
				<tr>
          <td valign="middle" class="hero hero-2 bg_white" style="padding: 4em 0;">
            <table>
            	<tr>
            		<td>
            			<div class="text" style="padding: 0 3em; text-align: center;">
            				<h2>Ciao <?= $model->getClient() ?>, in allegato trovi <span>il tuo preventivo</span></h2>
            			</div>
                  <div class="text" style="padding: 3em;">
                    <p>
                      Ho il piacere di presentarle la nostra collezione di orci realizzati e decorati a mano.<br>
                      L'orcio in ceramica contiene e custodisce i profumi e i sapori dell'olio extravergine di oliva biologico, prodotto nei nostri uliveti a <b>Trentinara e Giungano</b>.<br>

                      Scadenza offerta: <?= $model->formatDate($model->deadline) ?> <br>
                      Rimango a sua completa disposizione<br>
                      Cordiali Saluti<br>
                      <br>
                      Francesco Guariglia <br>
                      <br>
                      Mobile: + 39 3203828243<br>
                      Maria Guariglia<br>
                      mobile: +39 3807544300<br>
                      mail: e-commerce@ulivetodimaria.it<br>
                    </p>
                  </div>
            		</td>
            	</tr>
            </table>
          </td>
	      </tr><!-- end tr -->
	  </table>
      <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
      	<tr>
          <td valign="middle" class="bg_black footer email-section">
            <table>
            	<tr>
                <td valign="top" width="33.333%" style="padding-top: 20px;">
                  <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                      <td style="text-align: left; padding-right: 10px; color: white">
                      	<h3 class="heading">Chi siamo</h3>
                      	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi iaculis nisi at turpis lobortis, egestas mollis nunc lacinia</p>
                      </td>
                    </tr>
                  </table>
                </td>
                <td valign="top" width="33.333%" style="padding-top: 20px;">
                  <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                      <td style="text-align: left; padding-left: 5px; padding-right: 5px; color: white">
                      	<h3 class="heading">Contattaci</h3>
                      	<ul>
                            <li><span class="text">203 Fake St. Mountain View, San Francisco, California, USA</span></li>
                            <li><span class="text"><a href="tel:+2 392 3929 210">+2 392 3929 210</span></a></li>
                        </ul>
                      </td>
                    </tr>
                  </table>
                </td>
                <td valign="top" width="33.333%" style="padding-top: 20px;">
                  <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                      <td style="text-align: left; padding-left: 10px;">
                      	<h3 class="heading">Seguici su</h3>
                      	<ul>
                            <li><a href="https://www.facebook.com/ulivetotrentinara" style="text-decoration: none;"><span>Facebook</span></a></a></li>
                            <li><a href="https://www.instagram.com/aziendaluliveto/" style="text-decoration: none;"><span>Instagram</span></a></li>
                            <li><a href="https://www.ulivetodimaria.it" style="text-decoration: none;"><span>Web</span></a></li>
                        </ul>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr><!-- end: tr -->
        <tr>
        	<td valign="middle" class="bg_black footer email-section">
        		<table>
            	<tr>
                <td valign="top" width="33.333%">
                  <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                      <td style="text-align: left; padding-right: 10px;">
                      	<p>&copy; <?= date("Y") ?> Azienda Agricola L'Uliveto. Tutti i diritti riservati</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
        	</td>
        </tr>
      </table>

    </div>
  </center>
</body>
</html>