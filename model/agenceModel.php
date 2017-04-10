<?php

class agenceModel
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
  function add_agence($libelle, $departement)
  {

    $requete = $this->db->prepare
    ('
      INSERT INTO agence(libelle,departement)
      VALUES (?,?)
    ');

    $requete->execute(array($libelle, $departement));
    $requete->closeCursor();

    return "L'agence a bien été ajouté";
  }

  //liste des agences enregistrées dans la base
  function get_agences()
  {

    $requete = $this->db->prepare
    ('
      SELECT *
      FROM agence
    ');

    $requete->execute();

    return $requete->fetchAll();
  }

  function delete_agence($id)
  {

      $requete = $this->db->prepare
      ('
        DELETE FROM agence
        WHERE id=?
      ');

      $requete->execute(array($id));
      $requete->closeCursor();

      return "L'agence a bien été supprimée.";
  }

  // --------------------------------------------------------------------------
  // -----------------FIN DES METHODES DE GESTION DES THEMES-------------------
  // --------------------------------------------------------------------------

}
