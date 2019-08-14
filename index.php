<?php
require "controller/frontend/listPosts.php";
require "controller/frontend/post.php";
require "controller/backend/postEdit.php";
require "controller/frontend/connection.php";


if (isset($_GET["action"])) {
    if ($_GET["action"] == "listPosts") {
        listPosts();
    }
    elseif ($_GET["action"] == "post") {
        if (isset($_GET["id"]) && $_GET["id"] > 0) {
            post();
        }
        else {
            echo "Erreur : Aucun identifiant d'article envoyé.";
        }
    }
    elseif ($_GET["action"] == "editPost") {
            postEdit();
    }
    elseif ($_GET["action"] == "connection") {
            connection();
    }
    elseif ($_GET["action"] == "disconnection") {
        require "model/session.php";
        $session = new Session();
        $session->disconnect();
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