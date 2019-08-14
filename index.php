<?php
require "controller/frontend/listPosts.php";

if (isset($_GET["action"])) {
    if ($_GET["action"] == "listPosts") {
        listPosts();
    }
    elseif ($_GET["action"] == "post") {
        if (isset($_GET["id"]) && $_GET["id"] > 0) {
            require "controller/frontend/post.php";
            post();
        }
        else {
            echo "Erreur : Aucun identifiant d'article envoyé.";
        }
    }
    elseif ($_GET["action"] == "editPost") {
        require "controller/backend/postEdit.php";
        postEdit();
    }
    elseif ($_GET["action"] == "profil") {
        require "controller/frontend/profil.php";
        profil();
    }   
    elseif ($_GET["action"] == "inscription") {
        require "controller/frontend/inscription.php";
        inscription();
    }
    elseif ($_GET["action"] == "connection") {
        require "controller/frontend/connection.php";
        connection();
    }
    elseif ($_GET["action"] == "disconnection") {
        require "model/session.php";
        $session = new Session();
        $session->disconnect();
    }
    elseif ($_GET["action"] == "forgotPassword") {
        require "controller/frontend/forgotPassword.php";
        forgotPassword();
    }   
    elseif ($_GET["action"] == "resetPassword") {
        require "controller/frontend/resetPassword.php";
        resetPassword();
    }     
    elseif ($_GET["action"] == "addComment") {
        if (isset($_GET["id"]) && $_GET["id"] > 0) {
            if (!empty($_POST["author"]) && !empty($_POST["comment"])) {
                addComment($_GET["id"], $_POST["author"], $_POST["comment"]);
            }
            else {
                echo "Erreur : tous les champs ne sont pas remplis !";
            }
        }
        else {
            echo "Erreur : Aucun identifiant d'article envoyé.";
        }
    }
}
else {
    listPosts();
}

function loadClass($classname) {
    require "model/" . $classname . ".php";
}