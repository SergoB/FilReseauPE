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


//On a aussi besoin des méthodes de gestion des réponses
require_once("../model/reponseModel.php");
$reponseModel = new ReponseModel($connexion);


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

  //On va change automatiquement l'état de la demande quand on l'épingle
  $demandeModel->demande_switchEtat($_GET['id_demande'], 4);
}
//----------Fin de la gestion d'ajout d'un postit


//Quand on choisi d'archiver une demande
if (!empty($_GET['choixAction']) && $_GET['choixAction'] == 'archiver')
{
  //On va change automatiquement l'état de la demande quand on l'archive
  $demandeModel->demande_switchEtat($_GET['id_demande'], 4);
}
//------------FIN archivage


//Quand on valide le traitement d'une demande :
if (!empty($_POST['validerTraitement']))
{
    $reponseModel->add_reponse(
      $_POST['reponseDemande'],
      $_SESSION['user']['id'],
      $_GET['id_demande']);

      //On va change automatiquement l'état de la demande quand on la traite
      $demandeModel->demande_switchEtat($_GET['id_demande'], 4);
}
//----------FIN validation traitement


//Quand on valide une demande d'informations complémentares :
if (!empty($_POST['validerDemandeInfo']))
{
    $reponseModel->add_reponse(
      $_POST['reponseDemande'],
      $_SESSION['user']['id'],
      $_GET['id_demande']);

      //On va change automatiquement l'état de la demande quand on demande des infos complémentaires
      $demandeModel->demande_switchEtat($_GET['id_demande'], 2);
}
//--------------FIN demande d'info complémentaires


//Quand on valide un envoie au national :
if (!empty($_POST['validerEnvoiNational']))
{
    $reponseModel->add_reponse(
      $_POST['reponseDemande'],
      $_SESSION['user']['id'],
      $_GET['id_demande']);

      //On va change automatiquement l'état de la demande quand on envoie au national
      $demandeModel->demande_switchEtat($_GET['id_demande'], 3);
}
//-----------FIN envoie au national


$template = $twig -> loadTemplate ('expert/expertDemande.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'POST'=>$_POST,
    'GET'=>$_GET,
    'demande'=> $demandeModel->get_demande_byId($_GET['id_demande']),
    'reponses'=> $reponseModel->get_reponses_byDemande($_GET['id_demande']),

  ));
