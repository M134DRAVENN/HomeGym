<?php
Class Produit {

    private $db;
    private $insert;
    private $select;
    private $selectById;
    private $update;
    private $delete;
    private $selectLimit;
    private $selectCount; 
    private $recherche;
    private $selectIn;

    
    public function __construct($db){
        $this->db = $db;
        $this->insert = $this->db->prepare("insert into produit(designation, description, prix, idType, photo) values (:designation, :description, :prix, :idType, :photo)");
        $this->select = $db->prepare("select p.id, designation, description, prix, t.libelle as type, photo from produit p, type t where p.idType = t.id order by p.id");
        $this->selectById = $db->prepare("select p.id, designation, description, prix, t.libelle as type, photo from produit p, type t where p.id=:id");
        $this->update = $db->prepare("update produit set designation=:designation, description=:description, prix=:prix, idType=:idType, photo=:photo where id=:id");
        $this->delete = $db->prepare("delete from produit where id=:id");
        $this->selectLimit = $db->prepare("select p.id, designation, description, prix, t.libelle as type, photo from produit p, type t where p.idType = t.id order by designation limit :inf,:limite");
        $this->selectCount =$db->prepare("select count(*) as nb from produit");
        $this->recherche = $db->prepare("select p.id,designation,description,prix,photo,t.libelle as type from produit p,type t where p.idType = t.id and (designation like :recherche or description like :recherche) order by designation");
        $this->selectIn = $this->db->prepare("select id, designation, description, prix, photo, idType from produit where FIND_IN_SET(id, :ids)");
    }
    
    public function insert($designation, $description, $prix, $type, $photo) {
        $r = true;
        $this->insert->execute(array(':designation' => $designation, ':description' => $description, ':prix' => $prix, ':idType' => $type, ':photo' => $photo));
        if ($this->insert->errorCode() != 0) {
            print_r($this->insert->errorInfo());
            $r = false;
        }
        return $r;
    }

    public function select(){
        $this->select->execute();
        if ($this->select->errorCode()!=0){
            print_r($this->select->errorInfo());
        }
        return $this->select->fetchAll();
    }

    public function selectById($id) {
        $this->selectById->execute(array(':id' => $id));
        if ($this->selectById->errorCode() != 0) {
            print_r($this->selectById->errorInfo());
        }
        return $this->selectById->fetch();
    }

    public function update($id, $designation, $description, $prix, $type, $photo){
        $r = true;
        $this->update->execute(array(':id'=>$id,':designation'=>$designation,':prix'=>$prix ,':description'=>$description ,':idType'=>$type, ':photo' => $photo));
        if ($this->update->errorCode()!=0){ print_r($this->update->errorInfo());
        $r=false;
        }
        return $r;
    }

    public function delete($id){
        $r = true;
    
        $stmt = $this->db->prepare("SELECT idProduit FROM composer WHERE idProduit = :id");
        $stmt->execute(array(':id' => $id));
        $composerRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($composerRecords as $composerRecord) {
            $deleteComposerStmt = $this->db->prepare("DELETE FROM composer WHERE idProduit = :idProduit");
            $deleteComposerStmt->execute(array(':idProduit' => $composerRecord['idProduit']));
    
            if ($deleteComposerStmt->errorCode() != 0) {
                print_r($deleteComposerStmt->errorInfo());
                $r = false;
            }
        }
    
        $deleteProductStmt = $this->db->prepare("DELETE FROM produit WHERE id = :id");
        $deleteProductStmt->execute(array(':id' => $id));
    
        if ($deleteProductStmt->errorCode() != 0) {
            print_r($deleteProductStmt->errorInfo());
            $r = false;
        }
    
        return $r;
    }

    public function selectLimit($inf, $limite){
        $this->selectLimit->bindParam(':inf', $inf, PDO::PARAM_INT);
        $this->selectLimit->bindParam(':limite', $limite, PDO::PARAM_INT);
        $this->selectLimit->execute();
        if ($this->selectLimit->errorCode()!=0){
        print_r($this->selectLimit->errorInfo());
        }
        return $this->selectLimit->fetchAll();
    }

    public function selectCount(){
        $this->selectCount->execute();
        if ($this->selectCount->errorCode()!=0){
        print_r($this->selectCount->errorInfo());
        }
        return $this->selectCount->fetch();
    }

    public function recherche($recherche){
        $this->recherche->execute(array('recherche'=>'%'.$recherche.'%'));
        if ($this->recherche->errorCode()!=0){
        print_r($this->recherche->errorInfo());
        }
        return $this->recherche->fetchAll();
    }

    public function selectIn($ids){
        $implose = implode(',', $ids);
        $this->selectIn->bindParam(':ids', $implose);
        $this->selectIn->execute();
        if ($this->selectIn->errorCode()!=0){
        print_r($this->selectIn->errorInfo());
        }
        return $this->selectIn->fetchAll();
    }
}
?>