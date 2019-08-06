<?php 
class Session {

    public function __construct () {
        session_start();
    }

    public function setFlash ($message, $type) {
        $_SESSION["flash"] = array(
            "message" => $message,
            "type" => $type
        );    
    }

    public function flash() {
        if(isset($_SESSION["flash"])) {
            ?>
            <div id="msg-profil" class="alert alert-<?= $_SESSION["flash"]["type"] ?> alert-dismissible fade show" role="alert">                     
                <?= $_SESSION["flash"]["msg"] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> 
            </div>
            <?php
            echo $_SESSION["flash"];
            unset($_SESSION["flash"]);
        }
    }
}