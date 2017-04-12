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





}
