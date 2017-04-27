<?php

// Declaration class manager
class TacheModel
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
// -----------------METHODES DE GESTION DES TACHES-----------------------
// --------------------------------------------------------------------------

  //Ajoute une nouvelle tache avec les informations données en paramètre
  function add_tache($description,$deadline, $id_auteur, $fichiers)
  {
    $tache = $this->db->prepare
    ('
      INSERT INTO tache(description, deadline, estTraite, id_auteur)
      VALUES (:description, :deadline, :estTraite, :id_auteur)
    ');

    $tache->execute(
      array(
        'description'=>$description,
        'deadline'=>$deadline,
        'estTraite'=>0,
        'id_auteur'=>$id_auteur,
      ));

    $req_id = $this->db->prepare
    ('
    SELECT LAST_INSERT_ID() id
    ');

    $req_id->execute();

    $id_tache = $req_id->fetch();


    foreach ($fichiers as $fichier)
    {
      $reqFichiersTache = $this->db->prepare
      ('
      INSERT INTO fichiersTache(path,name,id_tache)
      VALUES (:path, :name, :id_tache)
      ');

      $reqFichiersTache->execute(
        array(
          'path' => $fichier['path'],
          'name' => $fichier['name'],
          'id_tache'=> $id_tache['id'],
          ));

      $reqFichiersTache->closeCursor();
    }
  }

  //récupère la liste des taches selon leur état (traité ou non)
  function get_taches($estTraite, $numPage, $nbparPage)
  {

    //On va afficher les 5 premieres taches puis  les autres selon le numéro de la page
    if ($numPage >= 1)
    {
      $firstResult = ($numPage - 1) * $nbparPage;
    }
    else
    {
      $firstResult = 0;
    }

    $taches = $this->db->prepare
    ('
      SELECT tache.id, description, deadline, estTraite, CONCAT(auteur.prenom," ", auteur.nom) as auteur, CONCAT(traiteur.prenom," ", traiteur.nom) as traiteur
      FROM tache
      JOIN utilisateur auteur ON tache.id_auteur = auteur.id
      LEFT JOIN utilisateur traiteur ON tache.id_traiteur = traiteur.id
      WHERE estTraite = ?
      ORDER BY deadline ASC
      LIMIT '.$firstResult. ',' . $nbparPage
    );

    $taches->execute(array($estTraite));

    return $taches->fetchAll();
  }


  //retourne le nombre de pages pour les themes
  function count_nbPage_taches($estTraite, $nbparPage)
  {
    $requete = $this->db->prepare
    ('
      SELECT count(*) resultat
      FROM tache
      WHERE estTraite = ?
    ');

    $requete->execute(array($estTraite));

    $nbTaches = $requete->fetch()['resultat'];

    return ceil($nbTaches/$nbparPage);
  }


  //récupère la liste des fichiers associés à une tache en particulier
  function get_fichiers_byTache($id_tache)
  {
      $fichiers = $this->db->prepare
      ('
        SELECT path,name
        FROM fichiersTache
        WHERE id_tache = ?
      ');

      $fichiers->execute(array($id_tache));

      return $fichiers->fetchAll();
  }


  //Fonction permettant de changer l'état d'une tache
  function tache_switchEtat($id, $etat, $traiteur)
  {
    //l'état peut être à 0 :  tache en attente
    //l'état peut être à 1 : tache effectuée
    $requete = $this->db->prepare
    ('
      UPDATE tache
      SET estTraite = :etat, id_traiteur = :traiteur
      WHERE id = :id
    ');

    $requete->execute(
      array(
        'id'=>$id,
        'etat'=>$etat,
        'traiteur'=>$traiteur
      ));
  }

  //Fonction qui retourne l'id de la tache qui est sur le point d'être ajoutée
  function get_current_tacheID()
  {
    $getID = $this->db->prepare
    ('
      SELECT max(id)+1 currentID
      FROM tache
    ');

    $getID->execute();

    return $getID->fetch()['currentID'];
  }



}
