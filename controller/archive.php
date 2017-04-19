<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();
session_start();


//Modèle des thèmes pour afficher la liste des thèmes dispo dans le tri
require_once("../model/themeModel.php");
$themeModel = new ThemeModel($connexion);

//On inclu également les demandes pour pouvoir afficher celles qui nous intéressent :
require_once("../model/demandeModel.php");
$demandeModel = new DemandeModel($connexion);

//Résultat affiché lorsque l'utilisateur trie selon certains critères
if (!empty($_POST['validerRecherche']))
{

  //On est sur la page 1 par défaut
  if (empty($_GET['numPage']))
  {
    $_GET['numPage'] = 1;
  }

  //On stock les données de recherche dans une variable de SESSION
  //Pour ne pas que ce soit perdu quand on passe sur les autres pages de résultat
  $_SESSION['rechercheData'] = array(
    'id_demande'=>$_POST['id_demande'],
    'keyword'=>$_POST['keyword'],
    'theme'=> $_POST['theme'],
  );

  $template = $twig -> loadTemplate ('archive.html.twig');
  echo $template -> render(
    array(
      'SESSION'=>$_SESSION,
      'POST'=>$_POST,
      'GET'=>$_GET,
      'themes'=>$themeModel->get_themes(1,1000),
      'demandesFiltrees'=>$demandeModel->recherche_demandes($_POST['id_demande'],$_POST['keyword'], $_POST['theme'], $_GET['numPage'], 10),
      'nbPages'=>$demandeModel->countPage_demandesFiltre(10, $_POST['id_demande'],$_POST['keyword'], $_POST['theme']),
      ));
}

//Par défaut on affiche la liste des demandes traitées
else
{
  //On est sur la page 1 par défaut
  if (empty($_GET['numPage']))
  {
    $_GET['numPage'] = 1;
  }

  //On gère les deux cas possible :
  //--> Recherche en cours et on affiche donc la page demandée des résultats de recherche
  //--> Utilisateur vient de se connecter et on affiche  par défaut toutes les demandes traitées
  if (!empty($_SESSION['rechercheData']))
  {
    //Ces deux variables tiennent compte de la recherche effectuée
    $demandesTraitees = $demandeModel->recherche_demandes($_SESSION['rechercheData']['id_demande'],$_SESSION['rechercheData']['keyword'], $_SESSION['rechercheData']['theme'], $_GET['numPage'], 10);
    $nbPages = $demandeModel->countPage_demandesFiltre(10, $_SESSION['rechercheData']['id_demande'],$_SESSION['rechercheData']['keyword'], $_SESSION['rechercheData']['theme']);
  }
  else
  {
    //On affiche l'ensemble des demandes traitées
    $demandesTraitees = $demandeModel->get_demandesAll_byEtat("Traitée", $_GET['numPage'], 10);
    $nbPages = $demandeModel->countPage_demandes(10,"Traitée");
  }

  $template = $twig -> loadTemplate ('archive.html.twig');
  echo $template -> render(
    array(
      'SESSION'=>$_SESSION,
      'POST'=>$_POST,
      'GET'=>$_GET,
      'themes'=>$themeModel->get_themes(1,1000),
      'demandesAll'=> $demandesTraitees,
      'nbPageDemandes'=> $nbPages,
    ));
}
