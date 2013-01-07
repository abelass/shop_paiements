<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_virement_charger_dist($options = array()){	
    $valeurs = $options;
return $valeurs;
}

function formulaires_virement_traiter_dist($options = array()){ 
    $id_commande=$options['id_commande'];
    $details=$options['details'];
    $valeurs = $options;

  //on institue la commande
  if($action = charger_fonction('instituer_commande', 'action',true)) {
        $action($id_commande."-attente");
        sql_updateq('spip_commandes',array('type_paiement'=>'virement'),'id_commande='.$id_commande);
      
    }    

    $montants=array();
    if(is_array($details)){
          foreach($details AS $detail){
              $montants[]=$detail['quantite']*$detail['prix'];
          }
      }



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
   $valeurs['editable']=false;
   
   // c'est tout bon, on envoie ca au pipeline pour traitements
    $valeurs=pipeline('traitement_paiements_forms', array(
            'args'=>array(
                'type_paiement' => 'virement',
            ),
            'data'=>$valeurs)
        );
   
 
    return $valeurs;
}


?>
