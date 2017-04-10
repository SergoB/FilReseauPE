<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//Permet d'empêcher la rééxécution d'une requête après actualisation page
include ('../model/empecherRepetitionPOST.php');

// -----------------------------------------------------------------------
// ------------------GESTION DES THEMES-----------------------------------
//------------------------------------------------------------------------

//Regroupe l'ensemble des méthodes permettant la gestion des tables de la bdd
require_once('../model/themeModel.php');
$themeModel = new themeModel($connexion);

  //Traitement de la requête d'ajout d'un nouveau thème
  if (!empty($_POST['libelleTheme'])) {
    $confirmAjoutTheme=$themeModel->add_theme($_POST['libelleTheme']);
  }
  else {
    $confirmAjoutTheme=""; //On n'affiche le message de confirmation que si un thème est ajouté.
  }

  //Traitement de la requête de suppression d'un thème
  if (!empty($_GET['id_theme'])) {
    $confirmSuprTheme=$themeModel->delete_theme($_GET['id_theme']);
  }
  else {
    $confirmSuprTheme="";
  }

//--------------------------------------------------------------------
//------------------FIN GESTION THEMES--------------------------------
//--------------------------------------------------------------------


// -----------------------------------------------------------------------
// ------------------GESTION DES MANAGERS---------------------------------
//------------------------------------------------------------------------

//Regroupe l'ensemble des méthodes permettant la gestion des tables de la bdd
require_once('../model/userModel.php');
$userModel = new userModel($connexion);

  //Traitement de la requête d'ajout d'un nouveau thème
  if (!empty($_POST['manager_id'])) {
    $confirmAjoutManager=$userModel->set_role($_POST['manager_id'],'Manager');
  }
  else {
    $confirmAjoutManager=""; //On n'affiche le message de confirmation que si un thème est ajouté.
  }

  //Traitement de la requête de suppression d'un thème
  if (!empty($_GET['manager_id'])) {
    $confirmSuprManager=$userModel->remove_role($_GET['manager_id']);
  }
  else {
    $confirmSuprManager="";
  }

//--------------------------------------------------------------------
//------------------FIN GESTION MANAGERS------------------------------
//--------------------------------------------------------------------


// -----------------------------------------------------------------------
// ------------------GESTION DES AGENCES---------------------------------
//------------------------------------------------------------------------

require_once('../model/agenceModel.php');
$agenceModel = new agenceModel($connexion);

  //Traitement de la requête d'ajout d'un nouveau thème
  if (!empty($_POST['libelleAgence'])) {
    $confirmAjoutAgence=$agenceModel->add_agence($_POST['libelleAgence'], $_POST['departementAgence']);
  }
  else {
    $confirmAjoutAgence=""; //On n'affiche le message de confirmation que si un thème est ajouté.
  }

  //Traitement de la requête de suppression d'un thème
  if (!empty($_GET['id_agence'])) {
    $confirmSuprAgence=$agenceModel->delete_agence($_GET['id_agence']);
  }
  else {
    $confirmSuprAgence="";
  }
//--------------------------------------------------------------------
//------------------FIN GESTION AGENCES------------------------------
//--------------------------------------------------------------------


$template = $twig -> loadTemplate ('admin/adminGestion.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'POST'=>$_POST,
    'GET'=>$_GET,
    'themeModel'=>$themeModel,
    'confirmAjoutTheme'=> $confirmAjoutTheme,
    'confirmSuprTheme'=> $confirmSuprTheme,
    'confirmAjoutAgence'=> $confirmAjoutAgence,
    'confirmSuprAgence'=> $confirmSuprAgence,
    'confirmAjoutManager'=> $confirmAjoutManager,
    'confirmSuprManager'=> $confirmSuprManager,
    'themes'=> $themeModel->get_themes(),
    'agences'=> $agenceModel->get_agences(),
    'managers'=> $userModel-> get_users_byRole('Manager'),
    'users' => $userModel-> get_users(),
  ));
