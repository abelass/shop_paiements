<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_atos_perl_charger_dist($options = array()){
    include_spip('inc/config');
    
    spip_log("paiment atos charger",'shop_paiements');

    session_start();
    
    $valeurs = $options;
    $details=$options['details'];
    //Reduire la référence comme atos ene permmet euelment 6 characters pour l'id
    $transaction_id = substr($options['transaction_id'], 0, 6);
    $monsite = $GLOBALS['meta']['nom_site'];
    $urlbase  = $GLOBALS['meta']['adresse_site'].'/';
    
    // N° de Marchand
    $merchant_id = lire_config('shop_paiements/merchant_id_atos_perl');
    
    $language = _request('lang');
    $pays=lire_config('shop_paiements/merchant_country_atos_perl');
    if($pays)$merchant_country = strtolower(sql_getfetsel('code','spip_pays','id_pays='.lire_config('shop_paiements/merchant_country_atos_perl')));
    else $merchant_country ='fr';
    

    $montants=array();
    if(is_array($details)){
          foreach($details AS $detail){
              $montants[]=$detail['quantite']*$detail['prix'];
          }
      }
    
    

    $pathbase =  str_replace(_DIR_PLUGIN_SHOP_PAIEMENTS.'formulaires','',dirname(__FILE__));

   //Calculer le prix
   $total=array_sum($montants).'00';


    $auto_return_url   = $urlbase . 'spip.php?action=confirmation_atos_perl';
    $normal_return_url = $urlbase . "spip.php?page=transaction_merci";
    $cancel_return_url = $urlbase . "spip.php?page=transaction_regret";

    // Devise: 978 = Euro
    $currency_code = 978;

    // URL vers les scripts Perl (.pl), avec '/' final
    $urlperl = $urlbase ._DIR_PLUGIN_SHOP_PAIEMENTS."paiement/atos_perl/conf/";

    // Path ABSOLUs vers les binaires ATOS, et vers le path file
    $pathbin = $pathbase.'/'._DIR_PLUGIN_SHOP_PAIEMENTS."paiement/atos_perl/bin/";
    $pathfile = $pathbase ._DIR_PLUGIN_SHOP_PAIEMENTS."paiement/atos_perl/conf/pathfile";
    //$pathfile = find_in_path("paiement/atos_perl/conf/pathfile");

// Version PERL
$parm = '';
$parm .= 'bindir='.$pathbin;
$parm .= '&'.'merchant_id='.$merchant_id;
$parm .= '&'.'merchant_country='.$merchant_country;
$parm .= '&'.'amount='.$total;
$parm .= '&'.'transaction_id='.$transaction_id;
$parm .= '&'.'currency_code='.$currency_code;
$parm .= '&'.'pathfile='.$pathfile;
$parm .= '&'.'normal_return_url='.urlencode($normal_return_url);
$parm .= '&'.'cancel_return_url='.urlencode($cancel_return_url);
$parm .= '&'.'automatic_response_url='.urlencode($auto_return_url);
$parm .= '&'.'language='.$language;
$parm .= '&'.'payment_means=CB,1,VISA,1,MASTERCARD,1';
$parm .= '&'.'header_flag=no';


$result = file_get_contents($urlperl."atos_request.pl?".$parm);


list($rien, $code, $error, $message, $rien) = explode('!', $result);


if (($code == "") && ($error == "")) {
    print ("<BR><CENTER>erreur appel request</CENTER><BR>");
    print ("executable request non trouve $pathbin");
}
else if ($code != 0) {
    print ("<center><b><h2>Erreur appel API de paiement.</h2></center></b>");
    print ("<br><br><br>");
    print ("Message erreur : $error <br>");
}
else {
    print ('<div class="logos_atos">'.$message.'</div>');
}


return $valeurs;
}

?>
