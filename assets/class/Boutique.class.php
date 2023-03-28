<?php 

class Boutique {
    private $_idArticle;
    private $_creditGiveArticle;
    private $_titleArticle;
    private $_descriptionArticle;
    private $_priceArticle;

    //Getter
    public function getIdArticle() {
        return $this->_idArticle;
    }
    public function getCreditGiveArticle() {
        return $this->_creditGiveArticle;
    }
    public function getTitleArticle() {
        return $this->_titleArticle;
    }
    public function getDescriptionArticle() {
        return $this->_descriptionArticle;
    }
    public function getPriceArticle() {
        return $this->_priceArticle;
    }

    //Setter
    public function setIdArticle($data) {
        if(is_int($data)) {
            if($data >= 0) {
                $this->_idArticle = htmlspecialchars($data);
            }
        }
    }
    public function setCreditGiveArticle($data) {
        if(is_int($data)) {
            if($data >= 0) {
                $this->_creditGiveArticle = htmlspecialchars($data);
            }
        }
    }
    public function setTitleArticle($data) {
        if(is_string($data)) {
            if(strlen($data) >= 5 && strlen($data) <= 60) {
                $this->_titleArticle = htmlspecialchars(strtolower($data));
            }
        }
    }
    public function setDescriptionArticle($data) {
        if(is_string($data)) {
            if(strlen($data) >= 5 && strlen($data) <= 1500) {
                $this->_descriptionArticle = htmlspecialchars(strtolower($data));
            }
        }
    }
    public function setPriceArticle($data) {
        if(is_float($data)) {
            if($data >= 1) {
                $this->_priceArticle = htmlspecialchars($data);
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