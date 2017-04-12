<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//On évite que la demande soit envoyée 2 fois en cas d'actualisation
include ('../model/empecherRepetitionPOST.php');

//On va ici avoir besoin des méthodes de gestion des DEMANDES
require_once("../model/demandeModel.php");
$demandeModel = new DemandeModel($connexion);

//Dans le cas d'une redirection suite à l'ajout d'un postit
//on vérifie que les champs ont été correctement validés et on ajoute le postit
require_once("../model/postitModel.php");
$postitModel = new PostitModel($connexion);

if (!empty($_POST['validerPostit']))
{
  $postitModel->add_postit(
    $_POST['titrePostit'],
    $_POST['questionType'],
    $_POST['reponseType'],
    $_SESSION['user']['id']);
}


$template = $twig -> loadTemplate ('expert/expertHome.html.twig');
echo $template -> render(
  array(
    'SESSION'=> $_SESSION,
    'demandes'=> $demandeModel->get_demandesAll_byEtat("En attente"),
    'POST'=>$_POST,

  ));
