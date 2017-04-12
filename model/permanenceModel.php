<?php

// Classe regroupant les méthodes de gestion des permanences
class PermanenceModel
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
// -----------------METHODES DE GESTION DES permanences---------------------------
// --------------------------------------------------------------------------



  //Ajoute une disponibilite pour un expert à une permanence donnée
  //Cette fonction n'est utilisée que dans la fonction "add_permanence"
  function add_dispo($idExpert, $idPermanence, $dispo)
  {
    $addDispo=$this->db->prepare
    ('
      INSERT INTO assurePermanence(id_expert, id_permanence, disponibilite)
      VALUES (:expert, :permanence, :dispo)
    ');

    $addDispo->execute(
      array(
        'expert'=>$idExpert,
        'permanence'=>$idPermanence,
        'dispo'=>$dispo
      ));
  }

  //Fonction retournant un id de permanence à partir d'une date doonnée
  function check_permanence($date)
  {
    $checkPerm=$this->db->prepare
    ('
      SELECT id
      FROM permanence
      WHERE datePerm = ?
    ');

    $checkPerm->execute(array($date));

    return $checkPerm->fetch()['id'];
  }



  //Fonction utilisée lors de l'ajout d'une nouvelle permanence
  function add_permanence($date, $expert, $dispo)
  {
    //On regarde d'abord si une permanence existe pour cette date
    $idPerm= $this->check_permanence($date);

    //Si le résultat est nul, la permanence n'existe pas
    if (!$idPerm)
    {
      //il faut donc la créer
      $addPerm=$this->db->prepare
      ('
        INSERT INTO permanence(datePerm)
        VALUES (?)
      ');

      $addPerm->execute(array($date));
      $addPerm->closeCursor();

      //On va maintenant chercher l'id de cette nouvelle permanence
      $idNewPerm = $this->check_permanence($date);

      //Puis on ajoute la disponibilité de l'expert à cette nouvelle permanence
      $this->add_dispo($expert,$idNewPerm,$dispo);

      return "Permanence créée avec succès et disponibilité ajoutée";
    }

    //Si une permanence existait à la date donnée,
    //on ajoute la disponibilité à celle-ci
    else
    {
      $this->add_dispo($expert, $idPerm,$dispo);
      return "Disponibilité bien ajoutée";
    }

    return "Le nouveau thème a bien été ajouté";
  }

  //Retourne la liste des permanences
  function get_permanences()
  {
    $permanences = $this->db->query
    ('
      SELECT *
      FROM permanence
    ');

      return $permanences->fetchAll();
  }

  //retourne la liste des experts affectés à une permanence
  function get_experts_withPermanences()
  {
    $experts=$this->db->query
    ('
    SELECT utilisateur.nom, utilisateur.prenom, disponibilite, datePerm
    FROM `assurePermanence`
    JOIN utilisateur on id_expert = utilisateur.id
    JOIN permanence on id_permanence = permanence.id
    ');

    return $experts->fetchAll();
  }




  // --------------------------------------------------------------------------
  // -----------------FIN DES METHODES DE GESTION DES permanenceS-------------------
  // --------------------------------------------------------------------------

}
