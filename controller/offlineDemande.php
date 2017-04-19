<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();
session_start();

//On va ici avoir besoin des méthodes de gestion des DEMANDES
require_once("../model/demandeModel.php");
$demandeModel = new DemandeModel($connexion);

//On a aussi besoin des méthodes de gestion des réponses
require_once("../model/reponseModel.php");
$reponseModel = new ReponseModel($connexion);


$template = $twig -> loadTemplate ('offlineDemande.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'POST'=>$_POST,
    'GET'=>$_GET,
    'demande'=> $demandeModel->get_demande_byId($_GET['id_demande']),
    'reponses'=> $reponseModel->get_reponses_byDemande($_GET['id_demande']),

  ));
