<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

session_start();

//--{ Verification du rôle utilisateur.
//On ne peut accéder à la page que si on est manager ou admin
if (empty($_SESSION['user']['role']) || $_SESSION['user']['role'] == 2)
{
    header('Location:index.php');
}
//--------Fin de la vérification du rôle }

//On va ici avoir besoin des méthodes de gestion des DEMANDES
require_once("../model/demandeModel.php");
$demandeModel = new DemandeModel($connexion);


//Le code ci-dessous est utile pour éviter le renvoi des informations après une actualisation de la page
// On rentre dans cette condition après chaque actualisation, lorsque $_POST  $_FILES ne sont pas vides
if(!empty($_POST) OR !empty($_FILES))
{
  //On rentre dans cette condition si on vient de valider l'envoi d'une demande
  if (!empty($_POST['validerDemande']))
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
          //On récupère l'ID de la demande qui va être créée
          $id_demande = $demandeModel->get_current_demandeID();
          //On défini le nom du futur dossier contenant les PJ
          $dossier = "../upload/demande/".$id_demande."/";

          //On créé un nouveau dossier pour la demande
          if (!is_dir($dossier))
          {
            mkdir($dossier, 0777);
          }

          $path_name = $dossier.$_SESSION['user']['id'].$_FILES['pj']['name'];
          //on ajoute ce fichier dans notre dossier upload
          $resultat = move_uploaded_file($_FILES['pj']['tmp_name'],$path_name);
          if ($resultat)
          {

            //On ajoute la demande
            $demandeModel->add_demande(
              $_POST['personneConcerne'],
              $_POST['idConcerne'],
              $_SESSION['user']['id'],
              $_POST['idTheme'],
              $_POST['description'],
              $path_name);

            $_SESSION['ConfirmationDemande'] = "Votre demande a bien été envoyée, elle sera traitée dans les plus brefs délais";
          }
          //Le fichier n'a pas pu être déplacé dans le dossier upload : erreur
          else
          {
            $_SESSION['ErreurDemande'] = "Erreur lors de l'ajout de la pièce jointe. La demande n'a pas pu être envoyée";
            header('Location: managerDemande.php');
            exit;
          }
        }
      }
      //L'extensiion du fichier n'est pas dans le tableau des formats autorisés
      else
      {
        $_SESSION['ErreurDemande'] = "Erreur : Le format ".$file_extension." n'est pas autorisé. La demande n'a pas pu être envoyée";
        header('Location: managerDemande.php');
        exit;
      }
    }

    //Aucune PJ, on ajoute la demande tout simplement
    else
    {
      $demandeModel->add_demande(
        $_POST['personneConcerne'],
        $_POST['idConcerne'],
        $_SESSION['user']['id'],
        $_POST['idTheme'],
        $_POST['description']);

      $_SESSION['ConfirmationDemande'] = "Votre demande a bien été envoyée, elle sera traitée dans les plus brefs délais";
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
if (isset($_SESSION['ConfirmationDemande']))
{
  $confirmationDemande = $_SESSION['ConfirmationDemande'];
  unset($_SESSION['ConfirmationDemande']);
}
//Si la variable de session n'a pas été affectée, ça signifie qu'aucune demande n'a été ajoutée
else
{
  $confirmationDemande = NULL;
}


$template = $twig -> loadTemplate ('manager/managerSuivi.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'POST'=>$_POST,
    'confirmationDemande'=>$confirmationDemande,
    'demandesEnAttente'=>$demandeModel->get_demandes_byManager($_SESSION['user']['id'],"En attente"),
    'demandesInfoComp'=>$demandeModel->get_demandes_byManager($_SESSION['user']['id'],"Demande d'infos complémentaires"),
    'demandesAuNational'=>$demandeModel->get_demandes_byManager($_SESSION['user']['id'],"Envoyée au national"),
    'demandesTraitees'=>$demandeModel->get_demandes_byManager($_SESSION['user']['id'],"Traitée"),


  ));
