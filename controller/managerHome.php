<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();
session_start();

//---------Verification du rôle utilisateur. 
//On ne peut accéder à la page que si on est manager ou admin
if (empty($_SESSION['user']['role']) || $_SESSION['user']['role'] == 2)
{
    header('Location:index.php');
}
//--------Fin de la vérification du rôle-------------

//Modèle des permanences pour gérer l'affichage du planning
require_once("../model/permanenceModel.php");
$permanenceModel = new permanenceModel($connexion);


$template = $twig -> loadTemplate ('manager/managerHome.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'expertsAvecPermanences'=>$permanenceModel->get_experts_withPermanences(),
  ));
