<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();
session_start();

require_once("../model/themeModel.php");
$themeModel = new ThemeModel($connexion);


$template = $twig -> loadTemplate ('manager/managerDemande.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'themes'=>$themeModel->get_themes(),
  ));
