<?php 
    session_start();
    session_destroy();

    // Redirige vers page d'accueil
    header("Location: index.php");

?>