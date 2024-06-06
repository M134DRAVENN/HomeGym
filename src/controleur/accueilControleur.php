<?php
function accueilControleur($twig){
    echo $twig->render('accueil.html.twig', array());
}
function contactControleur($twig){
    echo $twig->render('contact.html.twig', array());
}
function mention_legalControleur($twig){
    echo $twig->render('mentionlegal.html.twig', array());
}
function aproposControleur($twig){
    echo $twig->render('apropos.html.twig', array());
}
function maintenanceControleur($twig){
    echo $twig->render('maintenance.html.twig', array());
}
function rechercheControleur($twig, $db){

    $form = array();
    $produit = new Produit($db);

    if (isset($_GET['recherche'])) {
        $recherche = $_GET['recherche'];
        $resultats = $produit->recherche($recherche);
        echo $twig->render('recherche.html.twig', array('resultats' => $resultats, 'recherche' => $recherche, 'produit' => $produit));
        return;
    }
}
?>