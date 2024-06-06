<?php

function produitControleur($twig,$db){
    $form = array();
    $produit = new Produit($db);

    if(isset($_POST['btSupprimer'])){
        $cocher = $_POST['cocher'];
        $form['valide'] = true;
        foreach ( $cocher as $id){
            $exec=$produit->delete($id);
            if (!$exec){
                $form['valide'] = false;
                $form['message'] = 'Problème de suppression dans la table produit';
            }
        }
    }
    if(isset($_GET['id'])){
        $exec=$produit->delete($_GET['id']);
        if (!$exec){
            $form['valide'] = false;
            $form['message'] = 'Problème de suppression dans la table produit';
        }else{
            $form['valide'] = true;
            $form['message'] = 'Produit supprimé avec succès';
        }
    }
    $limite=5;
    if(!isset($_GET['nopage'])){
        $inf=0;
        $nopage=0;
    }
    else{
        $nopage=$_GET['nopage'];
        $inf=$nopage * $limite;
    }
    $r = $produit->selectCount();
    $nb = $r['nb'];
    $liste = $produit->selectLimit($inf,$limite);
    $form['nbpages'] = ceil($nb/$limite);
    $form['nopage'] = $nopage;
    echo $twig->render('Produit.html.twig', array('form'=>$form,'liste'=>$liste));
   }

function addProduitControleur($twig,$db){
    $form = array();
    if (isset($_POST['btAjouter'])){
        $photo = null;
        $produit = new Produit($db);
        $inputDesignation = $_POST['inputDesignation'];
        $inputDescription = $_POST['inputDescription'];
        $inputPrix = $_POST['inputPrix'];
        $idType = $_POST['idType'];

        $upload = new Upload(array('png', 'gif', 'jpg', 'jpeg', 'PNG', 'GIF', 'JPG', 'JPEG', 'jfif', 'JFIF'), 'images', 500000);
        $photo = $upload->enregistrer('photo');

        $exec=$produit->insert($inputDesignation, $inputDescription, $inputPrix, $idType, $photo['nom']);
        if (!$exec){
            $form['valide'] = false;
            $form['message'] = 'Problème d\'incsription';
        }
        else{
                $form['valide'] = true;
            }
        }
    echo $twig->render('addProduit.html.twig', array());
}

function produitModifControleur($twig, $db){
    $form = array();
    if(isset($_GET['id'])){
        $produit = new Produit($db);
        $unProduit = $produit->selectById($_GET['id']); 
        if ($unProduit!=null){
            $form['produit'] = $unProduit;
        }
        else{
            $form['message'] = 'produit incorrect';
        }
    }
    else{
        if(isset($_POST['btModifier'])){
            $photo = $_POST['photo'];
            $id = $_POST['id'];
            $designation = $_POST['inputDesignation'];
            $description = $_POST['inputDescription'];
            $prix = $_POST['inputPrix'];
            $type = $_POST['inputLibelle'];
            if(empty($designation)){
                $form['valide'] = false;
                $form['message'] = 'Le champ est vide';
            }
            else{
            $produit = new Produit($db);
            $exec=$produit->update($id, $designation, $description, $prix, $type, $photo);
                if(!$exec){
                    $form['valide'] = false;
                    $form['message'] = 'Echec de la modification';
                }else{
                    $form['valide'] = true;
                    $form['message'] = 'Modification réussie';
                }
            }
        }else{
            $form['message'] = 'produit non précisé';
        }
    }
    echo $twig->render('produit-modif.html.twig', array('form'=>$form));
}

function produitficheControleur($twig, $db){
    $form = array();
    $produit = new Produit($db);

    if(isset($_POST['btAjoutP'])){
        if(isset($_POST['id'])){
            $form['valideAjout']=true;
            $unProduit = $produit->selectById($_POST['id']);
            if(!$unProduit){
                $form['valideAjout']=false;
                $form['message'] = "Le produit n'existe pas";
            }else{
                $form['produit'] = $unProduit;
                if (isset($_SESSION['panier']) && is_array($_SESSION['panier'])) {
                    if (array_key_exists($unProduit['id'], $_SESSION['panier'])) {
                        $_SESSION['panier'][$unProduit['id']] ++;
                    }else {
                        $_SESSION['panier'][$unProduit['id']] = 1;
                    }
                }else {
                    $_SESSION['panier'] = array($unProduit['id'] => 1);
                }
                $form['message'] = "Le produit a bien été ajouté";
            }
        }else{
            $form['valideAjout']=false;
            $form['message'] = "Vous n'avez pas sélectionner de produit";
        }
    }else {

        if(isset($_GET['id'])){
            $unProduit = $produit->selectById($_GET['id']);
            if($unProduit!=null){
                $form['produit'] = $unProduit;
            }
            else{
                $form['message'] = 'Produit incorrect';
            }
        }
        else{
            $form['message'] = 'Produit non précisé';
        }

    }
    echo $twig->render('produit-fiche.html.twig', array('form'=>$form));
}
?>