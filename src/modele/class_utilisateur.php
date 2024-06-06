<?php
class Utilisateur{
    
    private $db;
    private $insert;
    private $connect;
    private $select;
    private $selectById;
    private $selectByEmail;
    private $update;
    private $updateMdp;
    private $delete;

 
    public function __construct($db){
        $this->db = $db;
        $this->insert = $this->db->prepare("insert into utilisateur(email, mdp, nom, prenom, idRole) values (:email, :mdp, :nom, :prenom, :role)");
        $this->connect = $this->db->prepare("select email, idRole, mdp from utilisateur where email=:email");
        $this->select = $db->prepare("select u.id, email, idRole, nom, prenom, r.libelle as libellerole from utilisateur u, role r where u.idRole = r.id order by nom");
        $this->selectById = $db->prepare("select id, email, nom, prenom, idRole from utilisateur where id=:id");
        $this->selectByEmail = $db->prepare("select id, email, nom, prenom, idRole from utilisateur where email=:email");
        $this->update = $db->prepare("update utilisateur set nom=:nom, prenom=:prenom, idRole=:role, email=:email, mdp=:mdp where id=:id");
        $this->deleteWithOrders = $db->prepare("delete from utilisateur where id=:id");
    }
 
    public function insert($email, $mdp, $role, $nom, $prenom){ 
        $r = true;
        $this->insert->execute(array(':email'=>$email, ':mdp'=>$mdp, ':role'=>$role,':nom'=>$nom, ':prenom'=>$prenom));
        if ($this->insert->errorCode()!=0){ print_r($this->insert->errorInfo());
            $r=false;
        }
 return $r;
}

    public function connect($email){
        $unUtilisateur = $this->connect->execute(array(':email'=>$email));
        if ($this->connect->errorCode()!=0){
            print_r($this->connect->errorInfo());
        }
        return $this->connect->fetch();
    }

    public function select(){
        $this->select->execute();
        if ($this->select->errorCode()!=0){
            print_r($this->select->errorInfo());
        }
        return $this->select->fetchAll();
    }

    public function selectById($id){
        $this->selectById->execute(array(':id'=>$id));
        if ($this->selectById->errorCode()!=0){
            print_r($this->selectById->errorInfo());
        }
        return $this->selectById->fetch();
    }

    public function selectByEmail($email){
        $this->selectByEmail->execute(array(':email'=>$email));
        if ($this->selectByEmail->errorCode()!=0){
            print_r($this->selectByEmail->errorInfo());
        }
        return $this->selectByEmail->fetch();
    }

    public function update($id, $role, $nom, $prenom, $email, $mdp){
        $r = true;
        $this->update->execute(array(':id'=>$id, ':role'=>$role, ':nom'=>$nom, ':prenom'=>$prenom, ':email'=>$email, ':mdp'=>$mdp));
        if ($this->update->errorCode()!=0) { 
            print_r($this->update->errorInfo());
            $r=false;
        }
        return $r;
    }

    public function deleteWithOrders($id){
        $r = true;
    
        $this->db->beginTransaction();
    
        $deleteOrders = $this->db->prepare("DELETE FROM commande WHERE idUtilisateur = :idUtilisateur");
    
        $deleteOrders->execute(array(':idUtilisateur' => $id));
    
        if ($deleteOrders->errorCode() != 0){
            print_r($deleteOrders->errorInfo());
            $this->db->rollBack();
            $r = false;
        }
    
        $deleteUser = $this->db->prepare("DELETE FROM utilisateur WHERE id = :id");
    
        $deleteUser->execute(array(':id' => $id));
    
        if ($deleteUser->errorCode() != 0){
            print_r($deleteUser->errorInfo());
            $this->db->rollBack();
            $r = false;
        } else {
            $this->db->commit();
        }
    
        return $r;
    }
}
?>