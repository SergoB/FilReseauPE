<?php

// Declaration class manager
class ReponseModel
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
// -----------------METHODES DE GESTION DES REPONSES-----------------------
// --------------------------------------------------------------------------

  //Ajoute une nouvelle reponse à une demande
  function add_reponse($contenu, $id_auteur, $id_demande, $pj=NULL)
  {

    $reponse = $this->db->prepare
    ('
      INSERT INTO reponse(contenu, date, id_auteur, id_demande, pj)
      VALUES (:contenu, :date, :id_auteur, :id_demande, :pj)
    ');

    $reponse->execute(
      array(
        'contenu'=>$contenu,
        'date'=>date('Y-m-d H:i:s'),
        'id_auteur'=>$id_auteur,
        'id_demande'=>$id_demande,
        'pj'=>$pj,
            ));

    $reponse->closeCursor();

  }


  //Récupérer la liste des réponses par date croissante et par id de demande
  function get_reponses_byDemande($id_demande)
  {

    $reponses= $this->db->prepare
    ('
      SELECT contenu, date, CONCAT(utilisateur.prenom, " ", utilisateur.nom) as auteur, utilisateur.role as auteurRole, pj
      FROM reponse
      JOIN utilisateur ON reponse.id_auteur = utilisateur.id
      WHERE id_demande = ?
      ORDER BY date ASC
    ');

    $reponses->execute(array($id_demande));

    return $reponses->fetchAll();
  }

}
