<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();


$template = $twig -> loadTemplate ('contact.html.twig');
echo $template -> render(
  array(

  ));
