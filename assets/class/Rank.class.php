<?php 

class Rank {
    private $_idRank;
    private $_nameRank;

    //Getter
    public function getIdRank() {
        return $this->_idRank;
    }
    public function getNameRank() {
        return $this->_nameRank;
    }

    //Setter
    public function setIdRank($Id) {
        if(is_int($Id)) {
            if($Id >= 0) {
                $this->_idRank = htmlspecialchars($Id);
            }
        }
    }
    public function setNameRank($Nom) {
        if(is_string($Nom)) {
            if(strlen($Nom) >= 5 && strlen($Nom) <= 60) {
                $this->_nameRank = htmlspecialchars(strtolower($Nom));
            }
        }
    }

    // Contructeur
    public function __construct(array $data) {
        $this->hydrate($data);
    }

    //ça récupérer les mutateurs et les passer dans un tableau
    public function hydrate(array $data) {
        foreach($data as $key => $value) {
            $method = "set$key";
            if(method_exists($this, $method)) {
                $this->$method($value);
            }
            else
            {
                trigger_error('Je trouve pas la méthode ! '.$key, E_USER_WARNING);
            }
        }
    }
}

?>