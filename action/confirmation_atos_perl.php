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
     //Insérer le type de paiement
     sql_updateq('spip_commandes', $valeurs, 'id_commande=' . sql_quote($transaction_id));
     
    //Éliminer le panier lié à la commande
    $reference = $flux['args']['paypal']['invoice'];
    $commande = sql_fetsel('id_commande, statut, id_auteur', 'spip_commandes', 'id_commande = '.sql_quote($transaction_id));
    $objet=sql_fetsel('objet,id_objet','spip_commandes_details','id_commande='.$commande['id_commande']);
    $id_panier=sql_getfetsel('id_panier','spip_paniers_liens','id_objet='.$objet['id_objet'].' AND objet='.sql_quote($objet['objet']));
    sql_delete('spip_paniers_liens','id_panier='.$id_panier);
    sql_delete('spip_paniers','id_panier='.$id_panier);
    spip_log("Retour atos_perl eliminer panier $id_panier",'shopaiements' . _LOG_INFO);

  
}
?>
