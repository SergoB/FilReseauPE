<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();
session_start();

//---------Verification du rôle utilisateur.
//On ne peut accéder à la page que si on est manager ou admin
if (empty($_SESSION['user']['role']) || $_SESSION['user']['role'] == 2)
{
  header('Location:index.php');
}
//--------Fin de la vérification du rôle-------------


//On va ici avoir besoin des méthodes de gestion des DEMANDES
require_once("../model/demandeModel.php");
$demandeModel = new DemandeModel($connexion);


//On a aussi besoin des méthodes de gestion des réponses
require_once("../model/reponseModel.php");
$reponseModel = new ReponseModel($connexion);

//Le code ci-dessous est utile pour éviter le renvoi des informations après une actualisation de la page
// On rentre dans cette condition après chaque actualisation, lorsque $_POST  $_FILES ne sont pas vides
if(!empty($_POST) OR !empty($_FILES))
{
  //On rentre dans cette condition si on vient de valider l'envoi d'une réponse
  if (!empty($_POST['validerReponse']))
  {
    //On procède à la vérification de la PJ si elle existe
    if (!empty($_FILES['pj']['name']))
    {
      //On va vérifier que ce fichier est au bon format
      $extensions_autorise = array('jpg','png','gif','pdf', 'csv', 'doc', 'ppt', 'zip', 'rar', 'xls', 'xlsx', 'docx');
      $file_extension = strtolower(substr(strrchr($_FILES['pj']['name'],'.'),1));
      if (in_array($file_extension,$extensions_autorise))
      {
        //On ne va ajouter le fichier que si celui-ci a bien été upload dans le dossier temporaire
        if (is_uploaded_file($_FILES['pj']['tmp_name']))
        {
          $path_name = "../upload/".$_SESSION['user']['id'].$_FILES['pj']['name'];
          //on ajoute ce fichier dans notre dossier upload
          $resultat = move_uploaded_file($_FILES['pj']['tmp_name'],$path_name);
          if ($resultat)
          {
            $reponseModel->add_reponse(
            $_POST['reponseDemande'],
            $_SESSION['user']['id'],
            $_GET['id_demande'],
            $path_name);

            //On change l'état de la demande à : "en attente"
            $demandeModel->demande_switchEtat($_GET['id_demande'], 1);

            $_SESSION['ConfirmationReponse'] = "Votre réponse a bien été envoyée.";
          }
          //Le fichier n'a pas pu être déplacé dans le dossier upload : erreur
          else
          {
            $_SESSION['ErreurReponse'] = "Erreur lors de l'ajout de la pièce jointe. La réponse n'a pas pu être envoyée";
          }
        }
      }
      //L'extensiion du fichier n'est pas dans le tableau des formats autorisés
      else
      {
        $_SESSION['ErreurReponse'] = "Erreur : Le format ".$file_extension." n'est pas autorisé. La réponse n'a pas pu être envoyée";
      }
    }

    //Aucune PJ, on ajoute la réponse tout simplement
    else
    {
      $reponseModel->add_reponse(
      $_POST['reponseDemande'],
      $_SESSION['user']['id'],
      $_GET['id_demande']);

      //On change l'état de la demande à : "en attente"
      $demandeModel->demande_switchEtat($_GET['id_demande'], 1);
      $_SESSION['ConfirmationReponse'] = "Votre réponse a bien été envoyée";
    }
  }

  //On affecte les variables de session qui sauvegardent les données contenues dans POST et FILES
  //Pour éviter que celles-ci soient perdu après la redirection
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


//Gestion du message de confirmation de l'envoi de réponse :
if (isset($_SESSION['ConfirmationReponse']))
{
  $confirmationReponse = $_SESSION['ConfirmationReponse'];
  unset($_SESSION['ConfirmationReponse']);
}
//Si la variable de session n'a pas été affectée, ça signifie qu'aucune réponse n'a été ajoutée
else
{
  $confirmationReponse = NULL;
}

//Gestion du message d'erreur:
if (isset($_SESSION['ErreurReponse']))
{
  $erreurReponse = $_SESSION['ErreurReponse'];
  unset($_SESSION['ErreurReponse']);
}
//Si la variable de session n'a pas été affectée, ça signifie qu'aucune réponse n'a été ajoutée
else
{
  $erreurReponse = NULL;
}


$template = $twig -> loadTemplate ('manager/managerConsulterDemande.html.twig');
  echo $template -> render(
    array(
      'SESSION'=>$_SESSION,
      'GET'=>$_GET,
      'POST'=>$_POST,
      'demande'=> $demandeModel->get_demande_byId($_GET['id_demande']),
      'reponses'=> $reponseModel->get_reponses_byDemande($_GET['id_demande']),
      'confirmationReponse'=> $confirmationReponse,
      'erreurReponse'=> $erreurReponse,
    ));
