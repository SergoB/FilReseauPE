<?php


class Theme
{
  private $id ;
  private $libelle;

  //Getter Theme
  public function id(){return $this->id;}
  public function libelle(){return $this->libelle;}
  //______________________________________

  //Setter Artiste
  public function setId($value){$this->id=$value;}
  public function setLibelle($value){$this->Libelle=$value;}
  //___________________________________________

  //Fonction en lien avec le constructeur pour instancier un objet à partir des données du champ dans la table
  public function hydrate(array $data)
  {
    foreach ($data as $key => $value) {
      $method = 'set'.$key;

      if (method_exists($this,$method)) {
        $this->$method($value);
      }
    }
  }

  public function __construct(array $data)
  {
    $this -> hydrate($data); //On passe un tableau de données en paramètre, le constructeur utilise la fonction hydrate pour attribuer les valeurs aux attributs
  }


}
