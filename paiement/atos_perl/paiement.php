<?php
	//Charger SPIP
	if (!defined('_ECRIRE_INC_VERSION')) {
		$currentdir = getcwd();
		// recherche du loader SPIP.
		$deep = 2;
		$lanceur ='ecrire/inc_version.php';
		$include = '../../'.$lanceur;
		while (!defined('_ECRIRE_INC_VERSION') && $deep++ < 6) { 
			// attention a pas descendre trop loin tout de meme ! 
			// plugins/zone/stable/nom/version/tests/ maximum cherche
			$include = '../' . $include;
			if (file_exists($include)) {
				chdir(dirname(dirname($include)));
				require $lanceur;
			}
		}	
	}
	if (!defined('_ECRIRE_INC_VERSION')) {
		die("<strong>Echec :</strong> SPIP ne peut pas etre demarre.<br />
			Vous utilisez certainement un lien symbolique dans votre repertoire plugins.");
	}	

    include_spip('inc/config');
    
	// N� de Marchand
	$merchant_id = lire_config('shop_paiements/atos_merchant_id');
    $path_bin = "bin/request";	
    $merchant_country= lire_config('shop_paiements/atos_merchant_country')?lire_config('shop_paiements/atos_merchant_country'):'fr';
    $transaction_id=urlencode($_SESSION['ref']);
    $currency_code=lire_config('shop_paiements/atos_currency_code')?lire_config('shop_paiements/atos_currency_code'):978;
    $pathfile='conf/pathfile';
    $normal_return_url=lire_config('shop_paiements/atos_normal_return_url')?lire_config('shop_paiements/atos_normal_return_url'):'?page=transaction_merci';
    $cancel_return_url=lire_config('shop_paiements/atos_cancel_return_url')?lire_config('shop_paiements/atos_cancel_return_url'):'?page=transaction_regret';
    $auto_return_url='plugins/shop_paiements/atos_perl/paiement_atos_confirmation.php';
    $language=_request('lang')?_request('lang'):'fr';
    $urlbase =$GLOBALS['meta']['url_site'] ;
    $urlperl = $urlbase . "conf/";
    
	session_start();
	
	$total = $_SESSION['total'];
		
	$total *= 100;
	
	
	print ("<HTML><HEAD><TITLE><:shop_paiements:titre_atos:></TITLE></HEAD>");
	print ("<BODY bgcolor=#ffffff>");
	print ("<Font color=#000000>");
	print ("<center><H1><:shop_paiements:atos_paiement_securise:></H1></center><br><br>");
	print ("<center><H1>" . $GLOBALS['meta']['nom_site'] . "</H1></center><br><br>");

    /*SPIP
	//		Affectation des param�tres obligatoires

	$parm="merchant_id=$merchant_id";
	$parm="$parm merchant_country=fr";
	$parm="$parm amount=$total";
	$parm="$parm currency_code=978";


	// Initialisation du chemin du fichier pathfile (� modifier)
    //   ex :
    //    -> Windows : $parm="$parm pathfile=c:\\repertoire\\pathfile";
    //    -> Unix    : $parm="$parm pathfile=/home/repertoire/pathfile";
    //
    // Cette variable est facultative. Si elle n'est pas renseign�e,
    // l'API positionne la valeur � "./pathfile".

		$parm="$parm pathfile=conf/pathfile";

	//		Si aucun transaction_id n'est affect�, request en g�n�re
	//		un automatiquement � partir de heure/minutes/secondes
	//		R�f�rez vous au Guide du Programmeur pour
	//		les r�serves �mises sur cette fonctionnalit�
	//
	
	#$parm="$parm transaction_id=" . urlencode($_SESSION['ref']);
	
	$path_bin = "bin/request";


	//	Appel du binaire request
	chdir($currentdir); // Il faut revenir dans le dossier du script de paiement pour trouver les binaires !!!!
	$result=exec("$path_bin $parm");

	//	sortie de la fonction : $result=!code!error!buffer!
	//	    - code=0	: la fonction g�n�re une page html contenue dans la variable buffer
	//	    - code=-1 	: La fonction retourne un message d'erreur dans la variable error

	//On separe les differents champs et on les met dans une variable tableau

	$tableau = explode ("!", "$result");

	//	r�cup�ration des param�tres

	$code = $tableau[1];
	$error = $tableau[2];
	$message = $tableau[3];

	//  analyse du code retour

  if (( $code == "" ) && ( $error == "" ) )
 	{
  	print ("<BR><CENTER>erreur appel request</CENTER><BR>");
  	print ("executable request non trouve $path_bin");
 	}

	//	Erreur, affiche le message d'erreur

	else if ($code != 0){
		print ("<center><b><h2>Erreur appel API de paiement.</h2></center></b>");
		print ("<br><br><br>");
		print (" message erreur : $error <br>");
	}

	//	OK, affiche le formulaire HTML
	else {
		print ("<br><br>");
		print ("  $message <br>");
	}

print ("</BODY></HTML>");*/


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

$result = file_get_contents($urlperl."/atos_request.pl?".$parm);

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
    print ("<br><br>");
    print ("  $message <br>");
}
print ("</BODY></HTML>");

?>
