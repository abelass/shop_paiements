<?php

function formulaires_cash_charger_dist($options = array()){
    
    spip_log("options du formulaire paypal : ".print_r($options,true),"paypal");    

    $valeurs = $options;
    

	
return $valeurs;
}

function formulaires_cash_traiter_dist($options = array()){ 
    $id_commande=$options['id_commande'];
    $details=$options['details'];
    $valeurs = $options;

    $montants=array();
    if(is_array($details)){
          foreach($details AS $detail){
              $montants[]=$detail['quantite']*$detail['prix'];
          }
      }

  
  $commandes=test_plugin_actif('commandes');
 
  if($commandes) sql_updateq('spip_commandes',array('type_paiement'=>'cash','statut'=>'attente'),'id_commande='.$id_commande);

    $montant=prix_formater(array_sum($montants));
  
    $panier=charger_fonction('supprimer_panier_encours','action/');
    $panier();
     $valeurs['message_ok']=_T('shop_paiements:cash_explication',array('montant'=>$montant)).'</div>';   
   $valeurs['envoi']='ok';
   $valeurs['editable']=false;
            // c'est tout bon, on envoie ca au pipeline pour traitements
    $valeurs=pipeline('traitement_paiements_forms', array(
            'args'=>array(
                'type_paiment' => 'cash',
            ),
            'data'=>$valeurs)
        );
   
	   // Notifications
    include_spip('inc/config');
    $config = lire_config('commandes');
    if ( $valeurs['envoi']=='ok') {
        $notifications = charger_fonction('notifications', 'inc');
        $options=array('type_paiement'=>'cash','details'=>$details);
        // Envoyer au vendeur et au client
        $notifications('commande_vendeur', $id_commande, $options);

        $notifications('commande_client', $id_commande, $options);
    }
    return $valeurs;
}


?>
