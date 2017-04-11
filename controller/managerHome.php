<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();
session_start();

//Modèle des permanences pour gérer l'affichage du planning
require_once("../model/permanenceModel.php");
$permanenceModel = new permanenceModel($connexion);


$template = $twig -> loadTemplate ('manager/managerHome.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'expertsAvecPermanences'=>$permanenceModel->get_experts_withPermanences(),
  ));
