<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//Permet d'empêcher la rééxécution d'une requête après actualisation page
include ('../model/empecherRepetitionPOST.php');

//Regroupe l'ensemble des méthodes permettant la gestion des tables de la bdd
require_once("../model/classManager.php");
$classManager = new ClassManager($connexion);


// -----------------------------------------------------------------------
// ------------------GESTION DES THEMES-----------------------------------
//------------------------------------------------------------------------

  //Traitement de la requête d'ajout d'un nouveau thème
  if (isset($_POST['libelleTheme'])) {
    $confirmAjoutTheme=$classManager->add_theme($_POST['libelleTheme']);
  }
  else {
    $confirmAjoutTheme=""; //On n'affiche le message de confirmation que si un thème est ajouté.
  }

  //Traitement de la requête de suppression d'un thème
  if (isset($_GET['id_theme'])) {
    $confirmSuprTheme=$classManager->delete_theme($_GET['id_theme']);
  }
  else {
    $confirmSuprTheme="";
  }

//--------------------------------------------------------------------
//------------------FIN GESTION THEMES--------------------------------
//--------------------------------------------------------------------

$template = $twig -> loadTemplate ('admin/adminGestion.html.twig');
echo $template -> render(
  array(
    'POST'=>$_POST,
    'GET'=>$_GET,
    'classManager'=>$classManager,
    'confirmAjoutTheme'=> $confirmAjoutTheme,
    'confirmSuprTheme'=> $confirmSuprTheme,
    'themes'=> $classManager->get_themes(),
  ));
