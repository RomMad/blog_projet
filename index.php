<?php
require "controller/frontend/listPosts.php";
require "controller/frontend/post.php";
require "controller/backend/postEdit.php";


if (isset($_GET["action"])) {
    if ($_GET["action"] == "listPosts") {
        listPosts();
    }
    elseif ($_GET["action"] == "post") {
        if (isset($_GET["id"]) && $_GET["id"] > 0) {
            post();
        }
        else {
            echo "Erreur : aucun identifiant de billet envoyé";
        }
    }
    elseif ($_GET["action"] == "editPost") {
        // if (isset($_GET["id"]) && $_GET["id"] > 0) {
            postEdit();
        // }
        // else {
        //     echo "Erreur : aucun identifiant de billet envoyé";
        // }
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
            echo "Erreur : aucun identifiant de billet envoyé";
        }
    }
}
else {
    listPosts();
}

function loadClass($classname) {
    require "model/" . $classname . ".php";
}