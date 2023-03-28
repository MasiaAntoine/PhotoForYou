<?php 

class PagesView {
    private $NamePage;
    private $IdSession;
    private $IdUser;
    private $DateVisit;

    //Getter
    public function getNamePage() {
        return $this->NamePage;
    }
    public function getIdSession() {
        return $this->IdSession;
    }
    public function getIdUser() {
        return $this->IdUser;
    }
    public function getDateVisit() {
        return $this->DateVisit;
    }

    //Setter
    public function setNamePage($data) {
        if(is_string($data)) {
            if(strlen($data) >= 5 && strlen($data) <= 100) {
                $this->NamePage = htmlspecialchars(strtolower($data));
            }
        }
    }
    public function setIdSession($data) {
        if(is_string($data)) {
            if(strlen($data) >= 5 && strlen($data) <= 100) {
                $this->IdSession = htmlspecialchars(strtolower($data));
            }
        }
    }
    public function setIdUser($data) {
        if(is_int($data)) {
            if($data >= 0) {
                $this->IdUser = htmlspecialchars($data);
            }
        }
    }
    public function setDateVisit($data) {
        $this->DateVisit = htmlspecialchars($data);
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