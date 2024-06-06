<?php
Class commande {

    private $db;
    private $insert;
    private $SelectByDateCli;


    public function __construct($db){
        $this->db = $db;
        $this->insert = $this->db->prepare("insert into commande(montant, date, etat, idUtilisateur) values (:montant, :date, :etat, :idUtilisateur)");
        $this->SelectByDateCli = $db->prepare("select * from commande where date=:date and idUtilisateur=:idUtilisateur");
    }

    public function insert($montant, $date, $etat, $idUtilisateur) {
        $r = true;
        $this->insert->execute(array(':montant' => $montant, ':date' => $date, ':etat' => $etat, ':idUtilisateur' => $idUtilisateur));
        if ($this->insert->errorCode() != 0) {
            print_r($this->insert->errorInfo());
            $r = false;
        }
        return $r;
    }

    public function selectByDateCli($date, $idUtilisateur) {
        $this->SelectByDateCli->execute(array(':date' => $date, ':idUtilisateur' => $idUtilisateur));
        if ($this->SelectByDateCli->errorCode() != 0) {
            print_r($this->SelectByDateCli->errorInfo());
        }
        return $this->SelectByDateCli->fetch();
    }
}
?>