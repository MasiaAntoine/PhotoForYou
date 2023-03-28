<?php 

class TagForPhoto {
    private $_idTag;
    private $_idPhoto;

    //Getter
    public function getIdTag() {
        return $this->_idTag;
    }
    public function getIdPhoto() {
        return $this->_idPhoto;
    }

    //Setter
    public function setIdTag($data) {
        if(is_int($data)) {
            if($data >= 0) {
                $this->_idTag = htmlspecialchars($data);
            }
        }
    }
    public function setIdPhoto($data) {
        if(is_int($data)) {
            if($data >= 0) {
                $this->_idPhoto = htmlspecialchars($data);
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