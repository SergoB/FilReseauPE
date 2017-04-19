<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();
session_start();

//--------Verification du rôle utilisateur.
//On ne peut accéder à la page que si on est manager ou admin
if (empty($_SESSION['user']['role']) || $_SESSION['user']['role'] == 2)
{
    header('Location:index.php');
}
//--------Fin de la vérification du rôle-------------

require_once("../model/themeModel.php");
$themeModel = new ThemeModel($connexion);


$template = $twig -> loadTemplate ('manager/managerDemande.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'themes'=>$themeModel->get_themes(1,1000),
  ));
