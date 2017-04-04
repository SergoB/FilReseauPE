<?php
require_once("infoConnect.php");

function dbconnect(){
  try {
    $bdd = new PDO('mysql:host=' .SERVER. ';dbname=' .DB. ';charset=utf8',USER, PSWD);

  } catch (Exception $e) {
    die('Erreur : ' .$e->getMessage());
  }
  return $bdd;
}
