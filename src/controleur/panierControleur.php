<?php
function panierControleur($twig,$db){
    $form = array();
    $liste = array();
    if(isset($_POST['placerCommade'])){
        $montant = $_POST['montant'];
        $aujourdhui = new DateTime();
        $aujourdhui->setTimezone(new DateTimeZone('Europe/Paris'));
        $date = $aujourdhui->format("Y-m-d H:i:s");
        $etat = 1;
        $utilisateur = new Utilisateur($db);
        $unUtil = $utilisateur->selectByEmail($_SESSION['login']);
        $idUtilisateur = $unUtil['id'];
        $form['valide'] = true;
        $commande = new Commande($db);
        $exec=$commande->insert($montant,$date,$etat,$idUtilisateur);
        if(!$exec){
            $form['valide'] = false;
            $form['message'] = 'Problème de l\'enregistremet de la commande';
        }else{
            $maCommande = $commande->selectByDateCli($date,$idUtilisateur);
            $composer = new Composer($db);
            foreach ($_SESSION['panier'] as $k => $v) {
                $execC = $composer->insert($maCommande['id'],$k,$v);
                if(!$execC){
                    $form['erreur'] = 'Problème : au moins un produit n\'a pas été validé';
                }
            }
            $form['message'] = 'Votre commande a été passée';
            unset($_SESSION['panier']);
        }
    }else{
        if (!empty($_SESSION['panier'])) {
           $ids = array_keys($_SESSION['panier']);
           $produits = new Produit($db);
           $liste = $produits->selectIn($ids);
        }

        if(isset($_GET['remove'])){
            unset($_SESSION['panier'][$_GET['remove']]);
        }

        if (isset($_POST['update'])) {
            foreach ($_POST as $k => $v) {
                if(strpos($k,'q-')!== false){
                    $explose = explode('-',$k);
                    $unid = (int)$explose[1];
                    $_SESSION['panier'][$unid] = $v;
                }
            }
            header("Location:index.php?page=panier");
        }
    }
    echo $twig->render('panier.html.twig', array('form'=>$form,'liste'=>$liste));
}
?>