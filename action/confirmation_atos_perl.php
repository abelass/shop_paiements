<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

spip_log("paiement retour 1",'shop_paiements');


function action_confirmation_atos_perl_dist($arg=null) {
    spip_log("paiement retour 2",'shop_paiements');
    include_spip('base/abstract_sql');
    include_spip('inc/config');
    $urlbase  = $GLOBALS['meta']['adresse_site'].'/';

    //Reduire la référence comme atos ene permmet euelment 6 characters pour l'id

    
    // N° de Marchand
    $merchant_id = lire_config('shop_paiements/merchant_id_atos_perl');
    
    $pathbase =  str_replace(_DIR_PLUGIN_SHOP_PAIEMENTS.'action','',dirname(__FILE__));

   //Calculer le prix
   $total=array_sum($montants).'00';



    // URL vers les scripts Perl (.pl), avec '/' final
    $urlperl = $urlbase ._DIR_PLUGIN_SHOP_PAIEMENTS."paiement/atos_perl/conf/";

    // Path ABSOLUs vers les binaires ATOS, et vers le path file
    $pathbin = $pathbase.'/'._DIR_PLUGIN_SHOP_PAIEMENTS."paiement/atos_perl/bin/";
    $pathfile = $pathbase ._DIR_PLUGIN_SHOP_PAIEMENTS."paiement/atos_perl/conf/pathfile";


$data= escapeshellcmd($_POST["DATA"]);

 spip_log($data,'shop_paiements');
 spip_log($pathbin,'shop_paiements');


spip_log("paiement retour params ",'shop_paiements');

// Appel du binaire response
$result = file_get_contents($urlperl."atos_response.pl?pathfile=$pathfile&message=$data&bindir=$pathbin");

$tableau = explode ("!", $result);

spip_log($tableau,'shop_paiements');

$code = $tableau[1];
$error = $tableau[2];
$merchant_id = $tableau[3];
$merchant_country = $tableau[4];
$amount = $tableau[5];
$transaction_id = $tableau[6];
$payment_means = $tableau[7];
$transmission_date= $tableau[8];
$payment_time = $tableau[9];
$payment_date = $tableau[10];
$response_code = $tableau[11];
$payment_certificate = $tableau[12];
$authorisation_id = $tableau[13];
$currency_code = $tableau[14];
$card_number = $tableau[15];
$cvv_flag = $tableau[16];
$cvv_response_code = $tableau[17];
$bank_response_code = $tableau[18];
$complementary_code = $tableau[19];
$return_context = $tableau[20];
$caddie = $tableau[21];
$receipt_complement = $tableau[22];
$merchant_language = $tableau[23];
$language = $tableau[24];
$customer_id = $tableau[25];
$order_id = $tableau[26];
$customer_email = $tableau[27];
$customer_ip_address = $tableau[28];
$capture_day = $tableau[29];
$capture_mode = $tableau[30];
$data = $tableau[31];

$valeurs=array('type_paiement'=>'atos');


  //on institue la commande
  if($action = charger_fonction('instituer_commande', 'action',true)) {
    spip_log('instituer la commande before switch','shop_paiements');
   switch($response_code){
   case '00':
      /* Transaction approved */
      spip_log('paye','shop_paiements');
      $action($transaction_id."-paye");
       break;
   case '12':
      /* Invalid amount
       PROBLEM LOCATION: MERCHANT */
       spip_log('partiel','shop_paiements');
      $action($transaction_id."-partiel");
      break;
      
      
  } 


    
}
     sql_updateq('spip_commandes', $valeurs, 'id_commande=' . sql_quote($transaction_id));


$logfile="/var/log/atos/site";

    // Ouverture du fichier de log en append

    $fp=fopen($logfile, "a");
    //  analyse du code retour
fputs($fp, "code $customer_ip_address \n");

  if (( $code == "" ) && ( $error == "" ) )
    {
    fwrite($fp, "erreur appel response\n");
    print ("executable response non trouve $path_bin\n");
    spip_log("erreur appel response\n",'shop_paiements');
    spip_log("executable response non trouve $path_bin\n",'shop_paiements');    
    }

    //  Erreur, sauvegarde le message d'erreur

    else if ( $code != 0 ){
        fwrite($fp, " API call error.\n");
        fwrite($fp, "Error message :  $error\n");
        spip_log(" API call error.\n",'shop_paiements');
        spip_log( "Error message :  $error\n",'shop_paiements');   
    }
    else {

    // OK, Sauvegarde des champs de la r�ponse

    fwrite( $fp, "merchant_id : $merchant_id\n");
    fwrite( $fp, "merchant_country : $merchant_country\n");
    fwrite( $fp, "amount : $amount\n");
    fwrite( $fp, "transaction_id : $transaction_id\n");
    fwrite( $fp, "transmission_date: $transmission_date\n");
    fwrite( $fp, "payment_means: $payment_means\n");
    fwrite( $fp, "payment_time : $payment_time\n");
    fwrite( $fp, "payment_date : $payment_date\n");
    fwrite( $fp, "response_code : $response_code\n");
    fwrite( $fp, "payment_certificate : $payment_certificate\n");
    fwrite( $fp, "authorisation_id : $authorisation_id\n");
    fwrite( $fp, "currency_code : $currency_code\n");
    fwrite( $fp, "card_number : $card_number\n");
    fwrite( $fp, "cvv_flag: $cvv_flag\n");
    fwrite( $fp, "cvv_response_code: $cvv_response_code\n");
    fwrite( $fp, "bank_response_code: $bank_response_code\n");
    fwrite( $fp, "complementary_code: $complementary_code\n");
    fwrite( $fp, "return_context: $return_context\n");
    fwrite( $fp, "caddie : $caddie\n");
    fwrite( $fp, "receipt_complement: $receipt_complement\n");
    fwrite( $fp, "merchant_language: $merchant_language\n");
    fwrite( $fp, "language: $language\n");
    fwrite( $fp, "customer_id: $customer_id\n");
    fwrite( $fp, "order_id: $order_id\n");
    fwrite( $fp, "customer_email: $customer_email\n");
    fwrite( $fp, "customer_ip_address: $customer_ip_address\n");
    fwrite( $fp, "capture_day: $capture_day\n");
    fwrite( $fp, "capture_mode: $capture_mode\n");
    fwrite( $fp, "data: $data\n");
    fwrite( $fp, "-------------------------------------------\n");
    spip_log(" paiement ok\n",'shop_paiements');
    }

    fclose ($fp);
  
}
?>
