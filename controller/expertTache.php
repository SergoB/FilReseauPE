<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

session_start();

//--------Verification du rôle utilisateur.
//On ne peut accéder à la page que si on est expert ou admin
if (empty($_SESSION['user']['role']) || $_SESSION['user']['role'] == 1)
{
  header('Location:index.php');
}
//--------Fin de la vérification du rôle-------------

//On aura besoin des opérations réalisables sur les tâches
require_once("../model/tacheModel.php");
$tacheModel = new TacheModel($connexion);


//Le code ci-dessous est utile pour éviter le renvoi des informations après une actualisation de la page
// On rentre dans cette condition après chaque actualisation, lorsque $_POST  $_FILES ne sont pas vides
if(!empty($_POST) OR !empty($_FILES))
{
  //On rentre dans cette condition si on vient de valider l'envoi d'une tache
  if (!empty($_POST['validerTache']))
  {
    //On procède à la vérification de la PJ si elle existe
    if (!empty($_FILES['tacheFichiers']['name'][0]))
    {
      //On va vérifier que ce fichier est au bon format
      $extensions_autorise = array('jpg','png','gif','pdf', 'csv', 'doc', 'ppt', 'zip', 'rar', 'xls', 'xlsx', 'docx');

      $total = count($_FILES['tacheFichiers']['name']);

      //On génère un tableau contenant tous les chemins des fichiers
      $listeCheminsFichiers = array();

      //On récupère l'ID de la tache qui va être créée
      $id_tache = $tacheModel->get_current_tacheID();
      //On défini le nom du futur dossier contenant les fichiers
      $dossier = "../upload/tache/".$id_tache."/";

      for ($i = 0; $i<$total; $i++)
      {
        $file_extension = strtolower(substr(strrchr($_FILES['tacheFichiers']['name'][$i],'.'),1));
        if (!in_array($file_extension,$extensions_autorise))
        {
          $_SESSION['ErreurTache'] = "Erreur : Le format ".$file_extension." n'est pas autorisé. La tache n'a pas pu être envoyée";
        }
        else
        {
          if (is_uploaded_file($_FILES['tacheFichiers']['tmp_name'][$i]))
          {
            //On stock tous les chemins
            $listeCheminsFichiers[$i]['path'] = $dossier . $_SESSION['user']['id'] . $_FILES['tacheFichiers']['name'][$i];
            $listeCheminsFichiers[$i]['name'] = $_FILES['tacheFichiers']['name'][$i];
          }
          else
          {
            $_SESSION['ErreurTache'] = "Erreur lors de l'ajout de la pièce jointe. La tache n'a pas pu être envoyée";
          }
        }
      }

      //On rentre dans cette boucle que si il n'y a pas eu d'erreur.
      if (empty($_SESSION['ErreurTache']))
      {
        //On créé un nouveau dossier pour la tache
        if (!is_dir($dossier))
        {
          mkdir($dossier, 0777);
        }


        for ($i = 0; $i<$total; $i++)
        {
            $resultat = move_uploaded_file($_FILES['tacheFichiers']['tmp_name'][$i],$listeCheminsFichiers[$i]['path']);

        }

      $tacheModel->add_tache(
          $_POST['descriptionTache'],
          $_POST['deadline'],
          $_SESSION['user']['id'],
          $listeCheminsFichiers
        );

      $_SESSION['ConfirmationTache'] = "La nouvelle tâche a bien été ajoutée";
      }
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

//On gère l'affichage du message de confirmation
if (isset($_SESSION['ConfirmationTache']))
{
  $confirmationTache = $_SESSION['ConfirmationTache'];
  unset($_SESSION['ConfirmationTache']);
}
//Si la variable de session n'a pas été affectée, ça signifie qu'aucune tache n'a été ajoutée
else
{
  $confirmationTache = NULL;
}

//On gère l'affichage du message d'erreur
if (isset($_SESSION['ErreurTache']))
{
  $erreurTache = $_SESSION['ErreurTache'];
  unset($_SESSION['ErreurTache']);
}
//Si la variable de session n'a pas été affectée, ça signifie qu'aucune erreur n'a été détectée
else
{
  $erreurTache = NULL;
}

//On indique qu'une tache est traitée pour la sortir de la liste
if (!empty($_GET['id_tache']))
{
  $tacheModel->tache_switchEtat($_GET['id_tache'],1, $_SESSION['user']['id']);
}

//Gestion de la pagintion : Si le numero de la page n'est pas précisé, on est sur la page 1
if (empty($_GET['numPage']))
{
  $_GET['numPage']=1;
}



$template = $twig -> loadTemplate ('expert/expertTache.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'POST'=>$_POST,
    'GET'=>$_GET,
    'FILES'=>$_FILES,
    'confirmationTache'=>$confirmationTache,
    'erreurTache'=>$erreurTache,
    'tachesEnAttente'=>$tacheModel->get_taches(0,$_GET['numPage'],5),
    'nbPagesTachesAttente'=>$tacheModel->count_nbPage_taches(0,5),
    //On récupère directement notre classe modèle
    //pour utiliser la fonction de récupération de fichiers selon son id de tache.
    'tacheModel'=>$tacheModel,
    'tachesRealisees'=>$tacheModel->get_taches(1, $_GET['numPage'],5),
    'nbPagesTachesRealise'=>$tacheModel->count_nbPage_taches(1,5),
  ));
