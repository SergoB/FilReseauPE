<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//On évite que la demande soit envoyée 2 fois en cas d'actualisation
include ('../model/empecherRepetitionPOST.php');


//---------Verification du rôle utilisateur.
//On ne peut accéder à la page que si on est manager ou admin
if (empty($_SESSION['user']['role']) || $_SESSION['user']['role'] == 2)
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

//Quand on valide l'ajout d'une réponse :
if (!empty($_POST['validerReponse']))
{
    $reponseModel->add_reponse(
      $_POST['reponseDemande'],
      $_SESSION['user']['id'],
      $_GET['id_demande']);

    //On change l'état de la demande à : "en attente"
    $demandeModel->demande_switchEtat($_GET['id_demande'], 1);

}

$template = $twig -> loadTemplate ('manager/managerConsulterDemande.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'GET'=>$_GET,
    'POST'=>$_POST,
    'demande'=> $demandeModel->get_demande_byId($_GET['id_demande']),
    'reponses'=> $reponseModel->get_reponses_byDemande($_GET['id_demande']),
  ));
