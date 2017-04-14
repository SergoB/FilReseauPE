<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//On évite que la demande soit envoyée 2 fois en cas d'actualisation
include ('../model/empecherRepetitionPOST.php');

//--------Verification du rôle utilisateur.
//On ne peut accéder à la page que si on est expert ou admin
if (empty($_SESSION['user']['role']) || $_SESSION['user']['role'] == 1)
{
    header('Location:index.php');
}
//--------Fin de la vérification du rôle-------------

//On va ici avoir besoin des méthodes de gestion des DEMANDES
require_once("../model/demandeModel.php");
$demandeModel = new DemandeModel($connexion);




$template = $twig -> loadTemplate ('expert/expertHome.html.twig');
echo $template -> render(
  array(
    'SESSION'=> $_SESSION,
    'demandes'=> $demandeModel->get_demandesAll_byEtat("En attente"),
    'POST'=>$_POST,

  ));
