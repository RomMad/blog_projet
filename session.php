<?php 
class Session {

    // Lancement de la session
    public function __construct () {
        session_start();
    }

    // Initialise un message d'alerte
    public function setFlash($message, $typeAlert) {
        if (!empty($_SESSION["flash"])) {
            $message =  $_SESSION["flash"]["message"] . "<br />" . $message;
        }
        $_SESSION["flash"] = array(
            "message" => $message,
            "typeAlert" => $typeAlert
        );
    }

    // Affiche le message d'alerte
    public function flash() {
        if (isset($_SESSION["flash"])) {
            ?>
            <div id="msg-profil" class="alert alert-<?= $_SESSION["flash"]["typeAlert"] ?> alert-dismissible fade show" role="alert">                     
                <?= $_SESSION["flash"]["message"] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> 
            </div>
            <?php
            unset($_SESSION["flash"]);
        }
    }
    // Déconnection de la session
    public function disconnect() {
        session_destroy();
        header("Location: connection.php"); // Redirige vers page d'accueil
    }

}