<?php

// Declaration class manager
class themeModel
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
// -----------------METHODES DE GESTION DES THEMES---------------------------
// --------------------------------------------------------------------------
  function add_theme($libelle)
  {

    $requete = $this->db->prepare
    ('
      INSERT INTO theme(libelle)
      VALUES (?)
    ');

    $requete->execute(array($libelle));
    $requete->closeCursor();

    return "Le nouveau thème a bien été ajouté";
  }


  function get_themes()
  {

    $requete = $this->db->prepare
    ('
      SELECT *
      FROM theme
    ');

    $requete->execute();

    return $requete->fetchAll();
  }

  function delete_theme($id)
  {

      $requete = $this->db->prepare
      ('
        DELETE FROM theme
        WHERE id=?
      ');

      $requete->execute(array($id));
      $requete->closeCursor();

      return "Le thème a bien été supprimé.";
  }

  // --------------------------------------------------------------------------
  // -----------------FIN DES METHODES DE GESTION DES THEMES-------------------
  // --------------------------------------------------------------------------

}
