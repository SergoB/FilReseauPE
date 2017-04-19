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


  //retourne la liste des themes
  function get_themes($numPage, $nbparPage)
  {
    //On va afficher les 10 premiers themes puis  les autres selon le numéro de la page
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
      FROM theme
      LIMIT '.$firstResult. ',' . $nbparPage
    );

    $requete->execute();

    return $requete->fetchAll();
  }

  //retourne le nombre de pages pour les themes
  function count_nbPage_themes($nbparPage)
  {
    $requete = $this->db->prepare
    ('
      SELECT count(*) resultat
      FROM theme
    ');

    $requete->execute();

    $nbThemes = $requete->fetch()['resultat'];

    return ceil($nbThemes/$nbparPage);
  }

  // --------------------------------------------------------------------------
  // -----------------FIN DES METHODES DE GESTION DES THEMES-------------------
  // --------------------------------------------------------------------------

}
