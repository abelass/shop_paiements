#!/usr/bin/perl

print "Content-Type: text/html\n\n";

$donnees = $ENV{"QUERY_STRING"};

@tab = split("&", $donnees);
%data = ();

$parm = "";
foreach $i (@tab){
    ($nom, $valeur) = split("=", $i);
    $nom =~ s/%(..)/pack("c", hex($1))/ge;
    $valeur =~ s/%(..)/pack("c", hex($1))/ge;
    $valeur =~ s/\+/ /g;
    $data{"$nom"} = $valeur;
	
	#print "$nom = $valeur<br/>";
	
	if ($nom eq "bindir") {
		next;
	}

	$parm = "$parm $nom=$valeur";
}

print "<pre>$data{'bindir'}request$parm</pre>";

system("$data{'bindir'}request $parm");

exit;