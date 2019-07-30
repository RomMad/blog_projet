<?php
// Vérifie si on est local ou en ligne
if ($_SERVER["HTTP_HOST"] == "localhost") {
    $dbHost = "localhost";
    $dbName = "blog_projet";
    $dbUser = "root";
    $dbPass = "";
} else {
    $dbHost = "db5000134112.hosting-data.io";
    $dbname = "dbs129050";
    $dbUser = "dbu50459";
    $dbPass = "!J3anF0r730r0ch3*";   
}
// Connexion à la base de données
try {
    $bdd = new PDO("mysql:host=" . $dbHost . ";dbname=" .  $dbName . ";charset=utf8", $dbUser, $dbPass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch(Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die("Erreur : ".$e->getMessage());
}