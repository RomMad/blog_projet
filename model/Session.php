<?php
namespace model;

class Session {

    // Lancement de la session
    public function __construct () {
    }

    // Connection de la session
    public function connect() {
        session_start();
    }

    // Initialise un message d'alerte
    public function setFlash($message, $typeAlert) {
        if (!isset($_SESSION["flash"])) {
            $Msg = 0;
          } else {
            $Msg = count($_SESSION["flash"]);
        }
        $_SESSION["flash"][$Msg] = array(
            "message" => $message,
            "typeAlert" => $typeAlert
        );
    }
    // Affiche le message d'alerte
    public function flash() {
        if (isset($_SESSION["flash"])) {
            foreach ($_SESSION["flash"] as $flash) {
                ?>
                <div id="msg-profil" class="alert alert-<?=$flash["typeAlert"] ?> alert-dismissible mb-3 fade show" role="alert">                     
                    <?= $flash["message"] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="TRUE">&times;</span>
                    </button> 
                </div>
                <?php
            }
            unset($_SESSION["flash"]);
        }
    }
    // Déconnection de la session
    public function disconnect() {
        session_destroy();
        header("Location: connection"); // Redirige vers page d'accueil
        exit;
    }
}