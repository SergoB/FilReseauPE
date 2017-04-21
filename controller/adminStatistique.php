<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

session_start();

//--------Verification du rôle utilisateur.
//On ne peut accéder à la page que si on est admin
if (empty($_SESSION['user']['role']) || $_SESSION['user']['role'] == 1 || $_SESSION['user']['role'] == 2)
{
  header('Location:index.php');
  exit;
}
//--------Fin de la vérification du rôle-------------


//==============Gestion des redirections après POST==============

// On rentre dans cette condition après chaque actualisation, lorsque $_POST  $_FILES ne sont pas vides
if(!empty($_POST) OR !empty($_FILES))
{
  //On rentre dans cette condition si on vient de valider l'upload
  if (!empty($_POST['validerUpload']))
  {
    //On va vérifier que ce fichier est au bon format
    $extensions_autorise = array('jpg','png','pdf', 'csv', 'doc', 'ppt');
    $file_extension = strtolower(substr(strrchr($_FILES['test']['name'],'.'),1));
    if (in_array($file_extension,$extensions_autorise))
    {
      $_SESSION['ValidFormatFile'] = "Le format de ce fichier a été accepté";
      //On ne va ajouter le fichier que si celui-ci a bien été upload dans le dossier temporaire
      if (is_uploaded_file($_FILES['test']['tmp_name']))
      {
        $path_name = "../upload/{$_FILES['test']['name']}";
        //Vu que c'est bien le cas, on ajoute ce fichier dans notre dossier upload
        $resultat = move_uploaded_file($_FILES['test']['tmp_name'],$path_name);
      }
    }
    else
    {
      $_SESSION['ValidFormatFile'] = "Le format ".$file_extension." n'est pas autorisé.";
    }
  }


  //On affecte ensuite les variables de session qui sauvegardent
  $_SESSION['sauvegarde'] = $_POST ;
  $_SESSION['sauvegardeFILES'] = $_FILES ;

  //On récupère le nom du fichier actuel
  $fichierActuel = $_SERVER['REQUEST_URI'] ;
  if(!empty($_SERVER['QUERY_STRING']))
  {
    $fichierActuel .= '?' . $_SERVER['QUERY_STRING'] ;
  }

  //On redirige vers ce fichier (et on supprime par la meme occasion les variables POST et FILES existantes)
  //pour éviter le renvoi en cas d'actualisation
  header('Location: ' . $fichierActuel);
  exit;
}

//--On arrive à cette partie qu'une fois qu'on a été redirigé logiquement
if(isset($_SESSION['sauvegarde']))
{
  //On redéfinit les variables $_POST et $_FILES telles qu'elles étaient avant la redirection
  $_POST = $_SESSION['sauvegarde'] ;
  $_FILES = $_SESSION['sauvegardeFILES'] ;

  //On désaffecte les variables de sauvegarde qui ne sont plus utiles
  unset($_SESSION['sauvegarde'], $_SESSION['sauvegardeFILES']);
}
//======Fin Gestion redirection=========

//La tentative d'envoie du fichier a normalement déjà eu lieu
//On va ici récupérer les différentes erreurs et confirmations.
if (!empty($_POST['validerUpload']))
{
    $path_name = "../upload/{$_FILES['test']['name']}";
    if (file_exists($path_name))
    {
      $confirmation = "le fichier a bien été ajouté";
    }
    else
    {
      $confirmation = "le fichier n'a pas pu être ajouté";
    }
}
else
{
  $confirmation = "Aucun fichier n'a été ajouté pour le moment";
}




$template = $twig -> loadTemplate ('admin/adminStatistique.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'POST'=>$_POST,
    'GET'=>$_GET,
    'verif_format'=> $_SESSION['ValidFormatFile'],
    'confirmation'=> $confirmation,
  ));
