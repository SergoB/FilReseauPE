<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();
session_start();

$template = $twig -> loadTemplate ('expert/expertDemande.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
  ));
