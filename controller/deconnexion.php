<?php
require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

session_start();

session_destroy();

header('Location: index.php');
exit();
