<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//Regroupe l'ensemble des méthodes permettant la gestion des tables de la bdd
require_once("../model/userModel.php");
$userModel = new userModel($connexion);

session_start();

require_once('connexion.php');
