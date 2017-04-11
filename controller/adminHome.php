<?php
setlocale (LC_TIME, 'fr_FR.utf8','fra');

require_once("../model/instancierTwig.php");

require_once("../model/dbconnect.php");
$connexion = dbconnect();

include("../model/empecherRepetitionPOST.php");

//On utilisera le modèle des utilisateurs pour récupérer la liste des experts
require_once("../model/userModel.php");
$userModel = new userModel($connexion);


// -----------------------------------------------------------------------
// ------------------GESTION DES PERMANENCES------------------------------
//------------------------------------------------------------------------
require_once("../model/permanenceModel.php");
$permanenceModel = new permanenceModel($connexion);

//Gestion de l'ajout d'une permanence
if (!empty($_POST['validerPermanence']))
{
  $confirmAjoutPermanence =
    $permanenceModel->add_permanence(
        $_POST['datePerm'],
        $_POST['id_expert'],
        $_POST['dispo']);
}
else {
  $confirmAjoutPermanence = "Aucune permanence ajouté";
}

//Gestion de l'affichage du tableau des permanences


$template = $twig -> loadTemplate ('admin/adminHome.html.twig');
echo $template -> render(
  array(
    'SESSION'=>$_SESSION,
    'experts'=> $userModel->get_users_byRole("Expert"),
    'confirmAjoutPermanence'=>$confirmAjoutPermanence,
    'permanences'=>$permanenceModel->get_permanences(),
    'expertsAvecPermanences'=>$permanenceModel->get_experts_withPermanences(),

  ));
