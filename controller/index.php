<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//Regroupe l'ensemble des méthodes permettant la gestion des tables de la bdd
require_once("../model/userModel.php");
$userModel = new UserModel($connexion);

session_start();

//On souhaite afficher la liste des postits pour les utilisateurs hors connexion
require_once("../model/postitModel.php");
$postitModel = new PostitModel($connexion);
if (!empty($_GET['id_postit']))
{
  $id_postit = $_GET['id_postit'];
}
else
{
  $id_postit = NULL;
}


//Toute la gestion de la page d'index se fait via la page connexion.php
//C'est sur cette page que l'on trouvera l'appel de la vue layout.html.twig
require_once('connexion.php');
