<?php

if (isset($_POST['validConnexion']))
{
  //Si les informations saisies ne correspondent à aucun utilisateur dans la base...
  if (!$userModel->check_user($_POST['email'], sha1($_POST['mdp'])))
  {
    $template = $twig -> loadTemplate ('layout.html.twig');
    echo $template -> render(
      array(
        'SESSION'=>$_SESSION,
        'GET'=>$_GET,
        'erreurConnect'=>"Identifiant ou mot de passe incorrect",
        'postits'=>$postitModel->get_postits(),
        'postit'=>$postitModel->get_postit($id_postit),
      ));
    }
    //Sinon, les informations saisies correspondent, on redirige vers l'espace adéquat
    else
    {
      $_SESSION['user'] = $userModel->get_user($_POST['email']);
      switch ($_SESSION['user']['role'])
      {
        case 1:
        header('Location: managerHome.php');
        exit();
        break;
        case 2:
        header('Location: expertHome.php');
        exit();
        break;
        case 3:
        header('Location: adminHome.php');
        exit();
        break;
        default:
        header('Location: index.php');
        exit();
        break;
      }
    }
  }
  //Par défaut si la personne n'essaie pas de se connecter
  else
  {

    $template = $twig -> loadTemplate ('layout.html.twig');
    echo $template -> render(
      array(
        'SESSION'=>$_SESSION,
        'GET'=>$_GET,
        'postits'=>$postitModel->get_postits(),
        'postit'=>$postitModel->get_postit($id_postit),
      ));
  }
