<?php

// Declaration class manager
class PostitModel
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
// -----------------METHODES DE GESTION DES POSTIT-----------------------
// --------------------------------------------------------------------------

  //Ajoute un nouveau postit
  function add_postit($titre, $questionType, $reponseType, $expert)
  {

    $postit = $this->db->prepare
    ('
      INSERT INTO postit(titre,questionType,reponseType, id_expert, date)
      VALUES (:titre, :questionType, :reponseType, :idExpert, :date)
    ');

    $postit->execute(
      array(
        'titre'=>$titre,
        'questionType'=>$questionType,
        'reponseType'=>$reponseType,
        'idExpert'=>$expert,
        'date'=>date('Y-m-d H:i:s'),
        ));

    $postit->closeCursor();
  }

  //liste des post-its avec les infos concernant l'auteur expert
  function get_postits($numPage,$nbparPage)
  {
    //On va afficher les 5 premiers postits puis  les autres selon le numéro de la page
    if ($numPage >= 1)
    {
      $firstResult = ($numPage - 1) * $nbparPage;
    }
    else
    {
      $firstResult = 0;
    }

    $postits = $this->db->prepare
    ('
      SELECT postit.id, titre, questionType, reponseType, date, CONCAT(utilisateur.prenom," ", utilisateur.nom) as expert
      FROM postit
      JOIN utilisateur ON postit.id_expert = utilisateur.id
      LIMIT '.$firstResult. ',' . $nbparPage
     );

    $postits->execute();

    return $postits->fetchAll();
  }

  //Retourne le nombre total de post-its
  function count_postits()
  {
    $requete = $this->db->prepare
    ('
      SELECT count(*) resultat
      FROM postit
    ');

    $requete->execute();

    return $requete->fetch()['resultat'];
  }

  //Retourne le nombre de pages d'affichage selon un nombre d'élément par page donné
  function count_nbPage($nbparPage)
  {
    $nbPostits = $this->count_postits();

    return ceil($nbPostits/$nbparPage);
  }

  // Retourne un post-it et ses informations selon son ID
  function get_postit($id_postit)
  {
      $postit = $this->db->prepare
      ('
        SELECT postit.id, titre, questionType, reponseType, date, CONCAT(utilisateur.prenom," ", utilisateur.nom) as expert
        FROM postit
        JOIN utilisateur ON postit.id_expert = utilisateur.id
        WHERE postit.id = ?
      ');

      $postit->execute(array($id_postit));

      return $postit->fetch();
  }
}
