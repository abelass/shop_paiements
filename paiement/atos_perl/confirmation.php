<?php
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

    spip_log("paiement retour confirmation.php",'shop_paiements');

    $action=charger_fonction('confirmation_atos_perl','action/');
    $panier();
?>
