<?php
// Appel des déclarations de chaques tables
require_once 'theme.class.php';


// Declaration class manager
class ClassManager
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


  function add_theme($libelle)
  {
    $SQL = <<<SQL
    INSERT INTO theme(libelle)
    VALUES (?)
SQL;

    $requete = $this->db->prepare($SQL);

    $requete->execute(array($libelle));
    $requete->closeCursor();

    return "Le nouveau thème a bien été ajouté";
  }


  function get_themes()
  {
    $SQL = <<<SQL
    SELECT *
    FROM theme
SQL;

    $requete = $this->db->prepare($SQL);
    $requete->execute();

    return $requete->fetchAll();
  }

  function delete_theme($id)
  {

      $SQL =<<<SQL
      DELETE FROM theme
      WHERE id=?
SQL;

      $requete = $this->db->prepare($SQL);

      $requete->execute(array($id));
      $requete->closeCursor();

      return "Le thème a bien été supprimé.";
  }

}
