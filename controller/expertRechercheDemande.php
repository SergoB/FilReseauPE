<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

session_start();

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

  $template = $twig -> loadTemplate ('expert/expertRechercheDemande.html.twig');
  echo $template -> render(
    array(
      'SESSION'=>$_SESSION,
      'POST'=>$_POST,
      'GET'=>$_GET,
      'themes'=>$themeModel->get_themes(1,1000),
      'demandesRecherches'=>$demandeModel->recherche_demandes($_POST['id_demande'],$_POST['keyword'], $_POST['theme'], $_GET['numPage'], 10),
      'nbPage'=>$demandeModel->countPage_demandesFiltre(10, $_POST['id_demande'],$_POST['keyword'], $_POST['theme']),
    ));
}
else
{

  //On affiche par défaut la page 1 même lorsque la variable GET n'est pas attribuée
  if (empty($_GET['numPage']))
  {
    $_GET['numPage'] = 1;
    //Bricolage pour éviter les erreurs indiquant que la variable de session est vide
    if (empty($_SESSION['rechercheData']))
    {
      $_SESSION['rechercheData'] = array(
        'id_demande'=>'',
        'keyword'=>'',
        'theme'=>'',
      );
    }
  }



  $template = $twig -> loadTemplate ('expert/expertRechercheDemande.html.twig');
  echo $template -> render(
    array(
      'SESSION'=>$_SESSION,
      'POST'=>$_POST,
      'GET'=>$_GET,
      'themes'=>$themeModel->get_themes(1,1000),
      'demandesRecherches'=>$demandeModel->recherche_demandes($_SESSION['rechercheData']['id_demande'],$_SESSION['rechercheData']['keyword'], $_SESSION['rechercheData']['theme'], $_GET['numPage'], 10),
      'nbPage'=>$demandeModel->countPage_demandesFiltre(10, $_SESSION['rechercheData']['id_demande'],$_SESSION['rechercheData']['keyword'], $_SESSION['rechercheData']['theme']),
    ));
}
