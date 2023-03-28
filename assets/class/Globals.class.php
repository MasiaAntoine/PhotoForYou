<?php 

class Globals {
    private $Remise;
    private $Costs;
    private $Tva;

    //Getter
    public function getRemise() {
        return $this->Remise;
    }
    public function getCosts() {
        return $this->Costs;
    }
    public function getTva() {
        return $this->Tva;
    }

    //Setter
    public function setRemise($data) {
        if(is_float($data)) {
            if($data >= 0) {
                $this->Remise = htmlspecialchars($data);
            }
        }
    }
    public function setCosts($data) {
        if(is_float($data)) {
            if($data >= 0) {
                $this->Costs = htmlspecialchars($data);
            }
        }
    }
    public function setTva($data) {
        if(is_float($data)) {
            if($data >= 0) {
                $this->Tva = htmlspecialchars($data);
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