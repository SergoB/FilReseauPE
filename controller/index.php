<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//Regroupe l'ensemble des méthodes permettant la gestion des tables de la bdd
require_once("../model/userModel.php");
$userModel = new UserModel($connexion);

session_start();

//Toute la gestion de la page d'index se fait via la page connexion.php
//C'est sur cette page que l'on trouvera l'appel de la vue layout.html.twig
require_once('connexion.php');
