<?php
function getPage($db){
    $lesPages['accueil'] = "accueilControleur;0";
    $lesPages['contact'] = "contactControleur;0";
    $lesPages['mention'] = "mentionControleur;0";
    $lesPages['politique'] = "politiqueControleur;0";
    $lesPages['inscrire'] = "inscrireControleur;0";
    $lesPages['maintenance'] = "maintenanceControleur;0";
    $lesPages['connexion'] = "connexionControleur;0";
    $lesPages['deconnexion'] = "deconnexionControleur;0";
    $lesPages['utilisateur'] = "utilisateurControleur;1";
    $lesPages['utilisateurModif'] = "utilisateurModifControleur;1";
    $lesPages['AddProduit'] = "addProduitControleur;1";
    $lesPages['Produit'] = "produitControleur;1";
    $lesPages['produitModif'] = "produitModifControleur;1";
    $lesPages['AddType'] = "addTypeControleur;1";
    $lesPages['Type'] = "typeControleur;1";
    $lesPages['typeModif'] = "typeModifControleur;1";
    $lesPages['recherche'] = "rechercheControleur;0";
    $lesPages['panier'] = "panierControleur;0";
    $lesPages['produitfiche'] = "produitFicheControleur;0";
    

    if ($db!=null){
        if (isset($_GET['page'])){
            $page = $_GET['page'];
        }else{
            $page = 'accueil';
        }
        if (!isset($lesPages[$page])){
            $page = 'accueil';
        }
        $explose = explode(";",$lesPages[$page]);
        $role = $explose[1];
        if ($role != 0){
            if(isset($_SESSION['login'])){
                if(isset($_SESSION['role'])){
                    if($role!=$_SESSION['role']){
                        $contenu = 'accueilControleur';
                    }else{
                        $contenu = $explose[0];
                    }
                }else{
                $contenu = 'accueilControleur';;
                }
            }else{
            $contenu = 'accueilControleur';;
            }
        }else{
            $contenu = $explose[0];
        }
        }else{
            $contenu = $lesPages['maintenance'];
    }
    return $contenu;
}
?>