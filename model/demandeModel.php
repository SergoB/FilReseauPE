<?php

// Declaration class manager
class DemandeModel
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
// -----------------METHODES DE GESTION DES DEMANDES-----------------------
// --------------------------------------------------------------------------

  //Ajoute une nouvelle demande avec les informations données en paramètre
  function add_demande($personneConcerne, $idConcerne, $idManager,
                       $idTheme,$description,$pj=NULL)
  {

    $demande = $this->db->prepare
    ('
      INSERT INTO demande(id_manager, personne_concerne,
      id_concerne, id_theme, id_etat, description, datePost, pj)
      VALUES (:idManager, :personneConcerne, :idConcerne,
      :idTheme, :idEtat, :description, :datePost, :pj)
    ');

    $demande->execute(
      array(
        'idManager'=>$idManager,
        'personneConcerne'=>$personneConcerne,
        'idConcerne'=>$idConcerne,
        'idTheme'=>$idTheme,
        'idEtat'=>1,
        'description'=>$description,
        'datePost'=>date('Y-m-d H:i:s'),
        'pj'=>$pj,
            ));

    $demande->closeCursor();

  }


  //Récupère la liste des demandes d'un manager pour un état donné en paramètre
  function get_demandes_byManager($id_manager, $etatDemande)
  {
    $demandes = $this->db->prepare
    ('
    SELECT demande.id, theme.libelle as libelleTheme, etatDemande.libelle as etatDemande,datePost
    FROM demande
    JOIN theme ON demande.id_theme = theme.id
    JOIN etatDemande ON demande.id_etat = etatDemande.id
    WHERE demande.id_manager = ?
    AND etatDemande.libelle = ?
    ');

    $demandes->execute(array($id_manager,$etatDemande));

    return $demandes->fetchAll();
  }


  //Récupère toutes les demandes selon un état donné en paramètre
  function get_demandesAll_byEtat($etatDemande)
  {
    $demandes = $this->db->prepare
    ('
    SELECT demande.id, theme.libelle as libelleTheme, etatDemande.libelle as etatDemande,datePost
    FROM demande
    JOIN theme ON demande.id_theme = theme.id
    JOIN etatDemande ON demande.id_etat = etatDemande.id
    WHERE etatDemande.libelle = ?
    ');

    $demandes->execute(array($etatDemande));

    return $demandes->fetchAll();
  }

  //Retourne un bloc d'informations relatif à une demande
  function get_demande_byId($id_demande)
  {
    $demande = $this->db->prepare
    ('
    SELECT demande.id, theme.libelle as libelleTheme, personne_concerne, id_concerne,
    etatDemande.libelle as etatDemande,datePost, CONCAT(utilisateur.prenom," ", utilisateur.nom) as manager, demande.description,
    utilisateur.email as emailManager, agence.nom as nomAgence, agence.departement
    FROM demande
    JOIN utilisateur ON demande.id_manager = utilisateur.id
    JOIN theme ON demande.id_theme = theme.id
    JOIN etatDemande ON demande.id_etat = etatDemande.id
    JOIN agence ON utilisateur.id_agence = agence.id
    WHERE demande.id = ?
    ');

    $demande->execute(array($id_demande));

    return $demande->fetch();
  }

  //Change l'état d'une demande avec un id_etat donné en paramètre
  function demande_switchEtat($id_demande, $id_etat)
  {
    $requete=$this->db->prepare
    ('
      UPDATE demande
      SET id_etat = :id_etat
      WHERE id = :id_demande
    ');

    $requete->execute(
      array(
        'id_etat'=>$id_etat,
        'id_demande'=>$id_demande,
      ));

    $requete->closeCursor();

  }

  //retourne une liste de demandes selon les infos données en paramètre
  function recherche_demandes($id_demande, $keyword, $theme)
  {
    if (empty($id_demande))
    {
      $condition_id_demande = 'ANY (SELECT id FROM demande)' ;
    }
    else
    {
      $condition_id_demande = $id_demande ;
    }

    if (is_null($keyword))
    {
      $condition_keyword = '"%"';
    }
    else
    {
      $condition_keyword = '"%' . $keyword . '%"';
    }


    if ($theme == "indifferent")
    {
      $condition_theme = 'ANY (SELECT id_theme FROM demande)';
    }
    else
    {
      $condition_theme = $theme;
    }


    $requete = $this->db->prepare
    ('
    SELECT demande.id, theme.libelle as libelleTheme, etatDemande.libelle as etatDemande,datePost
    FROM demande
    JOIN theme ON demande.id_theme = theme.id
    JOIN etatDemande ON demande.id_etat = etatDemande.id
    WHERE demande.id = '. $condition_id_demande. '
    AND description LIKE'. $condition_keyword . '
    AND demande.id_theme = '. $condition_theme
    );

    $requete->execute();

    return $requete->fetchAll();

  }





}
