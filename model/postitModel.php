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
  function get_postits()
  {
    $postits = $this->db->prepare
    ('
      SELECT postit.id, titre, questionType, reponseType, date, CONCAT(utilisateur.prenom," ", utilisateur.nom) as expert
      FROM postit
      JOIN utilisateur ON postit.id_expert = utilisateur.id
    ');

    $postits->execute();

    return $postits->fetchAll();
  }

}
