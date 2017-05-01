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
    const GET_GALLERY_BY_USER_EMAIL = "SELECT G.name, G.description, G.id, U.email FROM gallery AS G INNER JOIN user_gallery AS UG on G.id = UG.gallery_id INNER JOIN user AS U ON UG.user_id = U.id WHERE U.email = :email;";
    const GET_GALLERY_BY_USER_ID = "SELECT G.name, G.description, G.id, U.email FROM gallery AS G INNER JOIN user_gallery AS UG on G.id = UG.gallery_id INNER JOIN user AS U ON UG.gallery_id = U.id WHERE U.id = :uid;";
    const GET_X_GALLERIES        = "SELECT G.id, G.name, G.description FROM gallery ORDER BY G.id DESC LIMIT :num;";
    const GET_LAST_INSERTED_GALLERY_FOR_CONSTRAINT = "SELECT id FROM gallery ORDER BY id DESC LIMIT 1";
    
    const ADD_NEW_GALLERY = "INSERT INTO gallery (name, description) VALUES (:galleryName, :galleryDescription)";
    const ADD_USER_CONSTRAINT = "INSERT INTO user_gallery (user_id, gallery_id) VALUES (:uid, :gid)";
    
    const UPDATE_GALLERY_NAME = "UPDATE gallery SET name = :galleryName";
    const UPDATE_GALLERY_DESCRIPTION = "UPDATE gallery SET description = :galleryDescription";
    
    const DELETE_GALLERY_NAME    = "DELETE FROM gallery WHERE id = :id";
    
    const QUERY_FAIL = "We could not find this query";
    
    public function __construct($id = NULL, $name = NULL, $description = NULL) {
        
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }
    
    public static function addGallery($userId, $name, $description) {
        
        self::setQueryParameter(array('galleryName' => $name, 'galleryDescription' => $description));
        self::modelInsert(self::ADD_NEW_GALLERY_STATEMENT);
        $newGalleryId = self::modelSelect(self::GET_LAST_INSERTED_GALLERY_FOR_CONSTRAINT_STATEMENT);
        self::setQueryParameter(array('uid' => $userId, 'gid' => $newGalleryId));
        self::modelInsert(self::ADD_USER_CONSTRAINT_STATEMENT);
    }
    
    public static function getGalleryById($id) {
        
        self::setQueryParameter(array('idGallery' => $id));
        return self::modelSelect(self::GET_GALLERY_BY_ID_STATEMENT);
    }
    
    public static function getGalleriesByUserEmail($email) {
        
        self::setQueryParameter(array('email' => $email));
        return self::modelSelect(self::GET_GALLERY_BY_USER_EMAIL_STATEMENT);
    }
    
    public static function getGalleriesByUserId($id) {
        
        self::setQueryParameter(array('id' => $id));
        return self::modelSelect(self::GET_GALLERY_BY_USER_ID_STATEMENT);
    }
    
    public static function getGalleries($numberOfGalleries) {
        
        self::setQueryParameter(array('num' => $numberOfGalleries));
        return self::modelSelect(self::GET_X_GALLERIES_STATEMENT);
    }
    
    public static function deleteGalleryById($id) {
        
        self::setQueryParameter(array('id' => $id));
        return self::modelDelete(self::DELETE_GALLERY_BY_ID_STATEMENT);
    }
    
    public static function updateGallery($name = NULL, $description = NULL) {
        
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
    const GET_LAST_INSERTED_GALLERY_FOR_CONSTRAINT_STATEMENT = 5;
    
    private static function modelSelect($whichSelectStatement) {
        
        switch ($whichSelectStatement) {
            
            case self::GET_GALLERY_BY_ID_STATEMENT:
                $result = self::$database->performQuery(self::GET_GALLERY_BY_ID);
                return new Gallery($result[0]['id'], $result[0]['name'], $result[0]['description']);
                
            case self::GET_GALLERY_BY_USER_EMAIL_STATEMENT:
                $result = self::$database->performQuery(self::GET_GALLERY_BY_USER_EMAIL);
                return self::resultGalleryArray($result);
                
            case self::GET_GALLERY_BY_USER_ID_STATEMENT:
                $result = self::$database->performQuery(self::GET_GALLERY_BY_USER_ID);
                return self::resultGalleryArray($result);
                
            case self::GET_X_GALLERIES_STATEMENT:
                $result = self::$database->performQuery(self::GET_X_GALLERIES);
                return self::resultGalleryArray($result);
                
            case self::GET_LAST_INSERTED_GALLERY_FOR_CONSTRAINT_STATEMENT:
                $result = self::$database->performQuery(self::GET_LAST_INSERTED_GALLERY_FOR_CONSTRAINT);
                $id = $result[0]['id'];
                return intval($id);
                
            default:
                $_GET['Fail'] = self::QUERY_FAIL;
                return null;
        }
    }
    
    private static function resultGalleryArray($result) {
        $arrGalleries = array();
    
        foreach ($result as $gallery) {
            $gal = new Gallery();
            $gal->setId($gallery['id']);
            $gal->setName($gallery['name']);
            $gal->setDescription($gallery['description']);
            
            $arrGalleries[] = $gal;
        }
        
        return $arrGalleries;
    }
    
    const ADD_NEW_GALLERY_STATEMENT = 1;
    const ADD_USER_CONSTRAINT_STATEMENT = 2;
    
    private static function modelInsert($whichInsertStatement) {
        
        switch ($whichInsertStatement) {
            case self::ADD_NEW_GALLERY_STATEMENT:
                return self::$database->performQuery(self::ADD_NEW_GALLERY);
            case self::ADD_USER_CONSTRAINT_STATEMENT:
                return self::$database->performQuery(self::ADD_USER_CONSTRAINT);
            default:
                $_GET['Fail'] = self::QUERY_FAIL;
                return null;
        }
    }
    
    const UPDATE_NAME_STATEMENT = 1;
    const UPDATE_DESCRIPTION_STATEMENT = 2;
    
    private static function modelUpdate($whichUpdateStatement) {
        
        switch ($whichUpdateStatement) {
            case self::UPDATE_NAME_STATEMENT:
                self::$database->performQuery(self::UPDATE_GALLERY_NAME);
                break;
            case self::UPDATE_DESCRIPTION_STATEMENT:
                self::$database->performQuery(self::UPDATE_GALLERY_DESCRIPTION);
                break;
            default:
                $_GET['Fail'] = self::QUERY_FAIL;
                break;
        }
    }
    
    const DELETE_GALLERY_BY_ID_STATEMENT = 1;
    
    private static function modelDelete($whichDeleteStatement) {
        
        switch ($whichDeleteStatement) {
            case self::DELETE_GALLERY_BY_ID_STATEMENT:
                self::$database->performQuery(self::DELETE_GALLERY_NAME);
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