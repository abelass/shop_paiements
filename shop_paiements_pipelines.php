<?php
/**
 * Plugin Shop Paiements
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// Insérer le type de paiement
function shop_paiements_traitement_paypal($flux){
   
    $reference = $flux['args']['paypal']['invoice'];
    
    sql_updateq('spip_commandes',array('type_paiement'=>'paypal'),'reference='.sql_quote($reference));

    return $flux;
}
?>