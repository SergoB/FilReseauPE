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

//On souhaite afficher la liste des postits pour les managers
require_once("../model/postitModel.php");
$postitModel = new PostitModel($connexion);

//On stock dans une variable l'id de postit pour gérer le cas ou aucun postit en particulier n'est affiché
if (!empty($_GET['id_postit']))
{
  $id_postit = $_GET['id_postit'];
}
else
{
  $id_postit = NULL;
}

//Modèle des permanences pour gérer l'affichage du planning
require_once("../model/permanenceModel.php");
$permanenceModel = new permanenceModel($connexion);

//On est sur la page 1 par défaut
if (empty($_GET['numPage']))
{
  $_GET['numPage']=1;
}

$template = $twig -> loadTemplate ('manager/managerHome.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'GET'=>$_GET,
    'POST'=>$_POST,
    'expertsAvecPermanences'=>$permanenceModel->get_experts_withPermanences(),
    'postits'=>$postitModel->get_postits($_GET['numPage'],5),
    'nbPage'=>$postitModel->count_nbPage(5),
    'postit'=>$postitModel->get_postit($id_postit),
  ));
