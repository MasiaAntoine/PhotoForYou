<?php 

class Photo {
    private $_idPhoto;
    private $_creditPricePhoto;
    private $_isBuyPhoto;
    private $_titlePhoto;
    private $_descriptionPhoto;
    private $_datePublicPhoto;
    private $_idUserPhotographer;

    //Getter
    public function getIdPhoto() {
        return $this->_idPhoto;
    }
    public function getCreditPricePhoto() {
        return $this->_creditPricePhoto;
    }
    public function getIsBuyPhoto() {
        return $this->_isBuyPhoto;
    }
    public function getTitlePhoto() {
        return $this->_titlePhoto;
    }
    public function getDescriptionPhoto() {
        return $this->_descriptionPhoto;
    }
    public function getDdatePublicPhoto() {
        return $this->_datePublicPhoto;
    }
    public function getIdUserPhotographer() {
        return $this->_idUserPhotographer;
    }

    //Setter
    public function setIdPhoto($data) {
        if(is_int($data)) {
            if($data >= 0) {
                $this->_idPhoto = htmlspecialchars($data);
            }
        }
    }
    public function setCreditPricePhoto($data) {
        if(is_float($data)) {
            if($data >= 0) {
                $this->_creditPricePhoto = htmlspecialchars($data);
            }
        }
    }
    public function setIsBuyPhoto($data) {
        if(is_int($data)) {
            if($data >= 0) {
                $this->_isBuyPhoto = htmlspecialchars($data);
            }
        }
    }
    public function setTitlePhoto($data) {
        if(is_string($data)) {
            if(strlen($data) >= 5 && strlen($data) <= 60) {
                $this->_titlePhoto = htmlspecialchars(strtolower($data));
            }
        }
    }
    public function setDescriptionPhoto($data) {
        if(is_string($data)) {
            if(strlen($data) >= 5 && strlen($data) <= 1500) {
                $this->_descriptionPhoto = htmlspecialchars(strtolower($data));
            }
        }
    }
    public function setDatePublicPhoto($data) {
        $this->_datePublicPhoto = htmlspecialchars(strtolower($data));
    }
    public function setIdUserPhotographer($data) {
        if(is_int($data)) {
            if($data >= 0) {
                $this->_idUserPhotographer = htmlspecialchars($data);
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