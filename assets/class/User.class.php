<?php 

class User {
    private $_idUser;
    private $_surnameUser;
    private $_nameUser;
    private $_emailUser;
    private $_passwordUser;
    private $_rankUser;
    private $_creditUser;
    private $_reductionCreditUser;
    private $_isBanUser;

    //Getter
    public function getIdUser() {
        return $this->_idUser;
    }
    public function getSurnameUser() {
        return $this->_surnameUser;
    }
    public function getNameUser() {
        return $this->_nameUser;
    }
    public function getEmailUser() {
        return $this->_emailUser;
    }
    public function getPasswordUser() {
        return $this->_passwordUser;
    }
    public function getRankUser() {
        return $this->_rankUser;
    }
    public function getCreditUser() {
        return $this->_creditUser;
    }
    public function getReductionCreditUser() {
        return $this->_reductionCreditUser;
    }
    public function getIsBanUser() {
        return $this->_isBanUser;
    }

    //Setter
    public function setIdUser($Id) {
        if(is_int($Id)) {
            if($Id >= 0) {
                $this->_idUser = htmlspecialchars($Id);
            }
        }
    }
    public function setSurnameUser($Surname) {
        if(is_string($Surname)) {
            if(strlen($Surname) >= 3 && strlen($Surname) <= 60) {
                $this->_surnameUser = htmlspecialchars(strtolower($Surname));
            }
        }
    }
    public function setNameUser($Name) {
        if(is_string($Name)) {
            if(strlen($Name) >= 3 && strlen($Name) <= 60) {
                $this->_nameUser = htmlspecialchars(strtolower($Name));
            }
        }
    }
    public function setEmailUser($Email) {
        if(is_string($Email)) {
            if(strlen($Email) >= 3 && strlen($Email) <= 60) {
                $this->_emailUser = htmlspecialchars(strtolower($Email));
            }
        }
    }
    public function setPasswordUser($Password) {
        $Password = hash('sha256', $Password);

        if(is_string($Password)) {
            if(strlen($Password) >= 8 && strlen($Password) <= 150) {
                $this->_passwordUser = $Password;
            }
        }
    }
    public function setRankUser($Rank) {
        if(is_int($Rank)) {
            if($Rank >= 0) {
                $this->_rankUser = htmlspecialchars($Rank);
            }
        }
    }
    public function setCreditUser($Credit) {
        if(is_float($Credit)) {
            if($Credit >= 0) {
                $this->_creditUser = htmlspecialchars($Credit);
            }
        }
    }
    public function setReductionCreditUser($Credit) {
        if(is_int($Credit)) {
            if($Credit >= 0 && $Credit <= 50) {
                $this->_reductionCreditUser = htmlspecialchars($Credit);
            }
        }
    }
    public function setIsBanUser($value) {
        if(is_bool($value)) {
            $this->_reductionCreditUser = htmlspecialchars($value);
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