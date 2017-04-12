<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//On évite que la demande soit envoyée 2 fois en cas d'actualisation
include ('../model/empecherRepetitionPOST.php');

//On va ici avoir besoin des méthodes de gestion des DEMANDES
require_once("../model/demandeModel.php");
$demandeModel = new DemandeModel($connexion);

//On traite l'ajout de la nouvelle demande si nécéssaire

if (!empty($_POST['validerDemande']))
{
  $demandeModel->add_demande(
    $_POST['personneConcerne'],
    $_POST['idConcerne'],
    $_SESSION['user']['id'],
    $_POST['idTheme'],
    $_POST['description'],
    $_POST['pj']);
}

$template = $twig -> loadTemplate ('manager/managerSuivi.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'POST'=>$_POST,
    'demandesEnAttente'=>$demandeModel->get_demandes_byManager($_SESSION['user']['id'],"En attente"),
    'demandesInfoComp'=>$demandeModel->get_demandes_byManager($_SESSION['user']['id'],"Demande d'infos complémentaires"),
    'demandesAuNational'=>$demandeModel->get_demandes_byManager($_SESSION['user']['id'],"Envoyée au national"),
    'demandesTraitees'=>$demandeModel->get_demandes_byManager($_SESSION['user']['id'],"Traitée"),


  ));
