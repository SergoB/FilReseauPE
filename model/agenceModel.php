<?php

class AgenceModel
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
  function add_agence($nom, $departement)
  {

    $requete = $this->db->prepare
    ('
      INSERT INTO agence(nom,departement)
      VALUES (?,?)
    ');

    $requete->execute(array($nom, $departement));
    $requete->closeCursor();

    return "L'agence a bien été ajouté";
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


  //liste des agences enregistrées dans la base
  function get_agences($numPage, $nbparPage)
  {
    //On va afficher les 10 premieres agences puis  les autres selon le numéro de la page
    if ($numPage >= 1)
    {
      $firstResult = ($numPage - 1) * $nbparPage;
    }
    else
    {
      $firstResult = 0;
    }

    $requete = $this->db->prepare
    ('
      SELECT *
      FROM agence
      LIMIT '.$firstResult. ',' . $nbparPage
    );

    $requete->execute();

    return $requete->fetchAll();
  }


  //retourne le nombre de pages pour les agences
  function count_nbPage_agences($nbparPage)
  {
    $requete = $this->db->prepare
    ('
      SELECT count(*) resultat
      FROM agence
    ');

    $requete->execute();

    $nbAgences = $requete->fetch()['resultat'];

    return ceil($nbAgences/$nbparPage);
  }


  // --------------------------------------------------------------------------
  // -----------------FIN DES METHODES DE GESTION DES THEMES-------------------
  // --------------------------------------------------------------------------

}
