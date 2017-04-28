<?php

/**
 * User: Joel Häberli
 * Date: 17.03.2017
 * Time: 08:43
 */
class Gallery extends Model {
    
    private $id;
    private $name;
    private $description;
    
    const GET_GALLERY_BY_ID = "SELECT name, description FROM gallery WHERE id = :idGallery";
    const GET_GALLERY_BY_USER_EMAIL = "SELECT G.name, G.description, G.id, U.email FROM gallery AS G INNER JOIN user_gallery AS UG on G.id = UG.gallery_id INNER JOIN user AS U ON UG.gallery_id = U.id AND WHERE U.email = :email;";
    const GET_GALLERY_BY_USER_ID = "SELECT G.name, G.description, G.id, U.email FROM gallery AS G INNER JOIN user_gallery AS UG on G.id = UG.gallery_id INNER JOIN user AS U ON UG.gallery_id = U.id AND WHERE U.id = :uid;";
    const GET_X_GALLERIES        = "SELECT G.id, G.name, G.description FROM gallery ORDER BY G.id DESC LIMIT :num;";
    
    const ADD_NEW_GALLERY = "INSERT INTO gallery (name) VALUES (:galleryName)";
    
    const UPDATE_GALLERY_NAME = "UPDATE gallery SET name = :galleryName";
    const UPDATE_GALLERY_DESCRIPTION = "UPDATE gallery SET description = :galleryDescription";
    
    const DELETE_GALLERY_NAME    = "DELETE FROM gallery WHERE id = :id";
    
    //FAILS
    const QUERY_FAIL = "We could not find this query";
    
    public function __construct($id = NULL, $name = NULL, $description = NULL) {
        
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }
    
    public function addGallery(String $name, String $description) {
        
        self::setQueryParameter(array('galleryName' => $name, 'galleryDescription' => $description));
        return self::modelInsert(self::ADD_NEW_GALLERY_STATEMENT);
    }
    
    public function getGalleryById(Integer $id) {
        
        self::setQueryParameter(array('idGallery' => $id));
        return self::modelSelect(self::GET_GALLERY_BY_ID_STATEMENT);
    }
    
    public function getGalleriesByUserEmail(String $email) {
        
        self::setQueryParameter(array('email' => $email));
        return self::modelSelect(self::GET_GALLERY_BY_USER_EMAIL_STATEMENT);
    }
    
    public function getGalleriesByUserId(Integer $id) {
        
        self::setQueryParameter(array('id' => $id));
        return modelSelect(self::GET_GALLERY_BY_USER_ID_STATEMENT);
    }
    
    public function getNumberOfGalleries(Integer $number) {
        
        self::setQueryParameter(array('num' => $number));
        return self::modelSelect(self::GET_X_GALLERIES_STATEMENT);
    }
    
    public function deleteGalleryById(Integer $id) {
        
        self::setQueryParameter(array('id' => $id));
        return self::modelDelete(self::DELETE_GALLERY_BY_ID_STATEMENT);
    }
    
    public function updateGallery(String $name = NULL, String $description = NULL) {
        
        $itWorked = false;
        
        if (!($name == NULL)) {
            self::setQueryParameter(array('galleryName' => $name));
            self::modelUpdate(self::UPDATE_NAME_STATEMENT);
            $itWorked = true;
        }
        if (!($description == NULL)) {
            self::setQueryParameter(array('galleryDescription' => $description));
            self::modelUpdate(self::UPDATE_DESCRIPTION_STATEMENT);
            $itWorked = true;
        }
        return $itWorked;
    }
    
    const GET_GALLERY_BY_ID_STATEMENT = 1;
    const GET_GALLERY_BY_USER_EMAIL_STATEMENT = 2;
    const GET_GALLERY_BY_USER_ID_STATEMENT = 3;
    const GET_X_GALLERIES_STATEMENT = 4;
    
    private static function modelSelect(Integer $whichSelectStatement) {
        $g = new Gallery();
        switch ($whichSelectStatement) {
            case self::GET_GALLERY_BY_ID_STATEMENT:
                $result = self::$database->performQuery($g, self::GET_GALLERY_BY_ID);
                
                return new Gallery($result[0]['id'], $result[0]['name'], $result[0]['description']);
            case self::GET_GALLERY_BY_USER_EMAIL_STATEMENT:
                $result = self::$database->performQuery($g, self::GET_GALLERY_BY_USER_EMAIL);
                
                return self::resultGalleryArray($result);
            case self::GET_GALLERY_BY_USER_ID_STATEMENT:
                $result = self::$database->performQuery($g, self::GET_GALLERY_BY_USER_ID);
    
                return self::resultGalleryArray($result);
            case self::GET_X_GALLERIES_STATEMENT:
                $result = self::$database->performQuery($g, self::GET_X_GALLERIES);
    
                return self::resultGalleryArray($result);
            default:
                $_GET['Fail'] = self::QUERY_FAIL;
                break;
        }
    }
    
    private static function resultGalleryArray($result) {
        $arrGalleries = array();
    
        foreach ($result as $gallery) {
            $gal = new Gallery();
            $gal.setId($gallery['id']);
            $gal.setName($gallery['name']);
            $gal.setDescription($gallery['description']);
            $arrGalleries = array();
        }
        
        return $arrGalleries;
    }
    
    const ADD_NEW_GALLERY_STATEMENT = 1;
    
    private static function modelInsert(Integer $whichInsertStatement) {
        $g = new Gallery();
        switch ($whichInsertStatement) {
            case self::ADD_NEW_GALLERY_STATEMENT:
                return self::$database->performQuery($g, self::GET_GALLERY_BY_ID);
            default:
                $_GET['Fail'] = self::QUERY_FAIL;
                break;
        }
    }
    
    const UPDATE_NAME_STATEMENT = 1;
    const UPDATE_DESCRIPTION_STATEMENT = 2;
    
    private static function modelUpdate(Integer $whichUpdateStatement) {
        
        $g = new Gallery();
        switch ($whichUpdateStatement) {
            case self::UPDATE_NAME_STATEMENT:
                self::$database->performQuery($g, self::UPDATE_GALLERY_NAME);
                break;
            case self::UPDATE_DESCRIPTION_STATEMENT:
                self::$database->performQuery($g, self::UPDATE_GALLERY_DESCRIPTION);
                break;
            default:
                $_GET['Fail'] = self::QUERY_FAIL;
                break;
        }
    }
    
    const DELETE_GALLERY_BY_ID_STATEMENT = 1;
    
    private static function modelDelete(Integer $whichDeleteStatement) {
        
        $g = new Gallery();
        switch ($whichDeleteStatement) {
            case self::DELETE_GALLERY_BY_ID_STATEMENT:
                self::$database->performQuery($g, self::DELETE_GALLERY_NAME);
                break;
            default:
                $_GET['Fail'] = self::QUERY_FAIL;
                break;
        }
    }
    
    public function getId() {
        
        return $this->id;
    }
    
    public function setId($id) {
        
        $this->id = $id;
    }
    
    public function getName() {
        
        return $this->name;
    }
    
    public function setName($name) {
        
        $this->name = $name;
    }
    
    public function getDescription() {
        
        return $this->description;
    }
    
    public function setDescription($description) {
        
        $this->description = $description;
    }
}