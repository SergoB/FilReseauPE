<?php

// Declaration class manager
class ThemeModel
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

  //ajoute un thème
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

  //retourne la liste des themes
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

  //supprime un thème
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
