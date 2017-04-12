<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//On évite que la demande soit envoyée 2 fois en cas d'actualisation
include ('../model/empecherRepetitionPOST.php');

//On va ici avoir besoin des méthodes de gestion des DEMANDES
require_once("../model/demandeModel.php");
$demandeModel = new DemandeModel($connexion);


$template = $twig -> loadTemplate ('manager/managerConsulterDemande.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'demande'=> $demandeModel->get_demande_byId($_GET['id_demande']),

  ));
