<?php

function formulaires_virement_charger_dist($options = array()){	
    $valeurs = $options;
return $valeurs;
}

function formulaires_virement_traiter_dist($options = array()){ 
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
  if($commandes) sql_updateq('spip_commandes',array('type_paiement'=>'virement','statut'=>'attente'),'id_commande='.$id_commande);

   //Calculer le prix
   $montant=prix_formater(array_sum($montants));
  
   //Supprimer le panier en cours
    $panier=charger_fonction('supprimer_panier_encours','action/');
    $panier();
    
    //Le message de retour
   $valeurs['message_ok'].= _T('shop_paiements:explication_virement').'<br/>';
   $valeurs['message_ok'].= _T('shop_paiements:explication_montant',array('montant'=>$montant)).'<br/>'; 
   $valeurs['message_ok'].= _T('shop_paiements:explication_confirmation_mail').'<br/>';      
   $valeurs['message_ok'].= recuperer_fond('inclure/message_paiement_virement');   
   $valeurs['envoi']='ok';
   $valeurs['editable']=false;
   
   // c'est tout bon, on envoie ca au pipeline pour traitements
    $valeurs=pipeline('traitement_paiements_forms', array(
            'args'=>array(
                'type_paiement' => 'virement',
            ),
            'data'=>$valeurs)
        );
   
    // Notifications
    include_spip('inc/config');
    $config = lire_config('commandes');
    if ( $valeurs['envoi']=='ok') {
        $notifications = charger_fonction('notifications', 'inc', true);
        $options = array();
        if( $config['expediteur'] != "facteur" )
            $options['expediteur'] = $config['expediteur_'.$config['expediteur']];

        // Envoyer au vendeur et au client
        $notifications('commande_vendeur', $id_commande, $options);
        if($config['client'])
            $notifications('commande_client', $id_commande, $options);
    }
    return $valeurs;
}


?>
