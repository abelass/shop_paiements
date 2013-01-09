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

function shop_paiements_recuperer_fond($flux){

    $fond=$flux['args']['fond'] ;

    $texte=$flux['data']['texte'];
    $contexte=$flux['args']['contexte'];

    //Intervention dans l'affichage de la commande
    if ($fond == 'prive/objets/contenu/commande'){
        $patterns = array('#<div class="champ contenu_date_envoi#');
        $type_paiement=recuperer_fond('inclure/type_paiement',$contexte);        
        $replacements = array($type_paiement.'<div class="champ contenu_date_envoi');                      
        $flux['data']['texte'] = preg_replace($patterns,$replacements,$texte,1);
    }

    return $flux;
    }

function shop_paiements_insert_head($flux){
    // affichage du formulaire d'activation désactivation projets   

       $flux .= '<link rel="stylesheet" href="'.find_in_path('css/styles_shop_paiements.css').'" type="text/css" media="all" />';
    return $flux;   
}

?>