<?php

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

//Permet d'empêcher la rééxécution d'une requête après actualisation page
include ('../model/empecherRepetitionPOST.php');

//Regroupe l'ensemble des méthodes permettant la gestion des tables de la bdd
require_once("../model/userModel.php");
$userModel = new UserModel($connexion);

require_once("../model/agenceModel.php");
$agenceModel = new AgenceModel($connexion);

// -----------------------------------------------------------------------
// ------------------GESTION INSCRIPTION ---------------------------------
//------------------------------------------------------------------------

  if (isset($_POST['validerInscription']))
  {
    //Si l'adresse mail n'appartient pas déjà à un utilisateur...
    if (!$userModel->check_user_email($_POST['email']))
    {
      //Si les mdp saisis sont similaires on peut créer l'utlisateur
      if ($_POST['mdpConfirm'] == $_POST['mdp'])
      {
        $confirmInscription =
        $userModel->add_user
        (
          $_POST['email'],
          $_POST['nom'],
          $_POST['prenom'],
          sha1($_POST['mdp']),
          $_POST['agence']
        );
        //On stock l'utilisateur dans la variable de session pour qu'il soit connecté
        $_SESSION['user'] = $userModel->get_user($_POST['email']);


        //On souhaite afficher la liste des postits par défaut sur la page d'accueil
        require_once("../model/postitModel.php");
        $postitModel = new PostitModel($connexion);

        $template = $twig -> loadTemplate ('layout.html.twig');
        echo $template -> render(
          array(
            'confirmInscription'=>$confirmInscription,
            'SESSION'=>$_SESSION,
            'postits'=>$postitModel->get_postits(),
          ));
      }
      //Dans le cas inverse on renvoie vers la page d'inscription avec erreur
      else
      {
        $template = $twig -> loadTemplate ('inscription.html.twig');
        echo $template -> render(
          array(
            'SESSION'=>$_SESSION,
            'agences'=>$agenceModel->get_agences(),
            'erreurMdp'=> "Les deux mots de passe saisis doivent être identiques",
          ));
      }
    }
    //Si l'adresse mail appartient déjà à un utilisateur...
    //On redirige vers la page d'inscription avec message d'erreur.
    else
    {
      $template = $twig -> loadTemplate ('inscription.html.twig');
      echo $template -> render(
        array(
          'erreurMail'=> "Un utilisateur existe déjà avec cette adresse mail.",
          'SESSION'=>$_SESSION,
          'agences'=>$agenceModel->get_agences(),
        ));
    }
  }
  else {
    //Par défaut on renvoie vers la page d'inscription
    $template = $twig -> loadTemplate ('inscription.html.twig');
    echo $template -> render(
      array(
        'SESSION'=>$_SESSION,
        'agences'=>$agenceModel->get_agences(1,1000),
      ));
  }
