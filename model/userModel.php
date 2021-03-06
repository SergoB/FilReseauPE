<?php

// Classe regroupant les méthodes de gestion des utilisateurs
class UserModel
{
  private $db;

  public function setDB($db)
  {
    $this->db = $db;
  }

  function __construct($db)
  {
    $this->setDB($db);
  }


  // --------------------------------------------------------------------------
  // -----------------METHODES DE GESTION DES UTILISATEURS---------------------
  // --------------------------------------------------------------------------

    function add_user($email, $nom, $prenom, $mdp, $agence)
    {

      $requete = $this->db->prepare
      ('
      INSERT INTO utilisateur(email,nom,prenom,mdp, id_agence)
      VALUES (:email,:nom,:prenom,:mdp,:agence)
      ');

      $requete->execute(
        array(
          'email' => $email,
          'nom' => $nom,
          'prenom' => $prenom,
          'mdp' => $mdp,
          'agence' => $agence
        ));

      $requete->closeCursor();

      return "Votre compte a bien été créé";
    }


    //Retourne la liste des UTILISATEURS sous forme de tableau
    function get_users()
    {
      $users = $this->db->prepare
      ('
      SELECT *
      FROM utilisateur
      ');

      $users->execute();

      return $users->fetchAll();
    }


    //Retourne un objet "utilisateur" à partir de son adresse mail
    function get_user($email)
    {
      $user = $this->db->prepare
      ('
      SELECT *
      FROM utilisateur
      WHERE email = :email
      ');

      $user->execute(
        array(
          'email'=> $email
        ));

      return $user->fetch();
    }

    //vérifie que l'utilisateur existe dans la bdd
    function check_user($email,$mdp)
    {
      $req = $this->db->prepare
      ('
      SELECT *
      FROM utilisateur
      WHERE email= :email
      AND mdp = :mdp
      ');

      $req->execute(
        array(
        'email' => $email,
        'mdp' => $mdp
        ));

      $resultat = $req->fetch();
      if (!$resultat)
      {
          return False;
      }
      else
      {
        return True;
      }
    }

    //Permet de vérifier qu'une adresse mail n'est pas déjà dans la base
    function check_user_email($email)
    {
      $req = $this->db->prepare
      ('
      SELECT *
      FROM utilisateur
      WHERE email= :email
      ');

      $req->execute(
        array(
          'email'=>$email
        ));

      $resultat = $req->fetch();
      if (!$resultat)
      {
          return False;
      }
      else
      {
        return True;
      }
    }


    //Fixe un rôle à un utilisateur
    function set_role($user_id,$role)
    {
      $requete = $this->db->prepare
      ("
      UPDATE utilisateur
      SET utilisateur.role = (
        SELECT id
        FROM role_user
        WHERE libelle=:role)
      WHERE utilisateur.id=:user_id
      ");

      $requete->execute(
        array(
          'role'=>$role,
          'user_id'=>$user_id
        ));

      return "L'utilisateur n°".$user_id." est maintenant ".$role;
    }

    function remove_role($user_id)
    {
      $requete = $this->db->prepare
      ("
      UPDATE utilisateur
      SET utilisateur.role=NULL
      WHERE utilisateur.id=:id
      ");

      $requete->execute(
        array(
          'id'=>$user_id
        ));

      return "Le role de l'utilisateur n°".$user_id. " a été supprimé avec succès." ;
    }

    //Retourne la liste des utilisateurs selon un role donné en paramètre
    function get_users_byRole($role, $numPage, $nbparPage)
    {

      //On va afficher les 10 premieres utilisateurs puis  les autres selon le numéro de la page
      if ($numPage >= 1)
      {
        $firstResult = ($numPage - 1) * $nbparPage;
      }
      else
      {
        $firstResult = 0;
      }

      $users = $this->db->prepare
      ('
      SELECT utilisateur.id, utilisateur.nom, prenom, email, agence.nom as nomAgence, departement as departementAgence
      FROM utilisateur
      LEFT JOIN role_user ON utilisateur.role = role_user.id
      LEFT JOIN agence ON utilisateur.id_agence = agence.id
      WHERE role_user.libelle = :role
      LIMIT '.$firstResult. ',' . $nbparPage
      );

      $users->execute(
        array(
          'role'=>$role,
        ));

        return $users->fetchAll();
      }

      //retourne le nombre de pages pour les utilisateurs d'un role
      function count_nbPage_users_byRole($role,$nbparPage)
      {
        $requete = $this->db->prepare
        ('
          SELECT count(*) resultat
          FROM utilisateur
          JOIN role_user ON utilisateur.role = role_user.id
          WHERE role_user.libelle = ?
        ');

        $requete->execute(array($role));

        $nbUsers = $requete->fetch()['resultat'];

        return ceil($nbUsers/$nbparPage);
      }


    // --------------------------------------------------------------------------
    // -----------------FIN DES METHODES DE GESTION DES UTILISATEURS-------------
    // --------------------------------------------------------------------------


}
