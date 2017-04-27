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


//On aura besoin de la liste des post-its
require_once("../model/postitModel.php");
$postitModel = new PostitModel($connexion);


//Gestion de la suppression des postits
if (!empty($_GET['id_postit']) && $_GET['choix']=='delete' )
{
  $postitModel->delete_postit($_GET['id_postit']);
  $confirmSuppression = "Le postit n°".$_GET['id_postit']." a bien été supprimé";
}
else
{
  $confirmSuppression = NULL;
}

//Gestion de la modification des postits
if (!empty($_GET['id_postit']) && $_GET['choix']=='edit' )
{
  $postit= $postitModel->get_postit($_GET['id_postit']);
}
else
{
  $postit = NULL;
}

//Confirmation de la modification du postit
if (!empty($_POST['validerModif']))
{
  $postitModel->edit_postit($_POST['id_postit'], $_POST['titrePostit'], $_POST['questionType'], $_POST['reponseType']);
}


//On est sur la page 1 par défaut
if (empty($_GET['numPage']))
{
  $_GET['numPage'] = 1;
}

$template = $twig -> loadTemplate ('expert/expertPostits.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'POST'=>$_POST,
    'GET'=>$_GET,
    'postits'=> $postitModel->get_postits($_GET['numPage'], 10),
    'nbPage'=>$postitModel->count_nbPage(10),
    'confirmSuppression' => $confirmSuppression,
    'postit'=> $postit,
  ));
