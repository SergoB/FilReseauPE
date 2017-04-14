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

//On va ici avoir besoin des méthodes de gestion des THEMES
require_once("../model/themeModel.php");
$themeModel = new ThemeModel($connexion);

if (!empty($_POST['validerRecherche']))
{
  $template = $twig -> loadTemplate ('expert/expertRechercheDemande.html.twig');
  echo $template -> render(
    array(
      'SESSION'=>$_SESSION,
      'POST'=>$_POST,
      'GET'=>$_GET,
      'themes'=>$themeModel->get_themes(),
      'demandesRecherches'=>$demandeModel->recherche_demandes($_POST['id_demande'],$_POST['keyword'], $_POST['theme']),
    ));
}
else
{
  $template = $twig -> loadTemplate ('expert/expertRechercheDemande.html.twig');
  echo $template -> render(
    array(
      'SESSION'=>$_SESSION,
      'POST'=>$_POST,
      'GET'=>$_GET,
      'themes'=>$themeModel->get_themes(),
    ));
}
