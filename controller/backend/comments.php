<?php 

function comments() {
    
    spl_autoload_register("loadClass");

    $session = new Session();
    $usersManager = new UsersManager();
    $commentsManager = new CommentsManager();

    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["userID"])) {
        header("Location: index.php");
    } else {
        // Récupère le rôle de l'utilisateur
        $userRole = $usersManager->getRole($_SESSION["userID"]);
        if ($userRole != 1) {
            header("Location: index.php");
        }
    }

    if (!empty($_POST)) {
        if (!empty($_POST["action_apply"]) && isset($_POST["selectedComments"])) {
            // Supprime les commentaires sélectionnés via une boucle
            if ($_POST["action_apply"] == "delete" && isset($_POST["selectedComments"])) {
                foreach ($_POST["selectedComments"] as $selectedComment) {
                    $comment = new Comments([
                        "id" => $selectedComment,
                    ]);
                    $commentsManager->delete($comment);
                }
                // Compte le nombre de commentaires supprimés pour adaptés l'affichage du message
                $nbselectedComments = count($_POST["selectedComments"]);
                if ($nbselectedComments>1) {
                    $session->setFlash($nbselectedComments . " commentaires ont été supprimés.", "warning");
                } else {
                    $session->setFlash("Le commentaire a été supprimé.", "warning");
                }
            }
            // Modère les commentaires sélectionnés via une boucle
            if ($_POST["action_apply"] == "moderate" && isset($_POST["selectedComments"])) {
                foreach ($_POST["selectedComments"] as $selectedComment) {
                    $comment = new Comments([
                        "id" => $selectedComment,
                        "status" => 2,
                        ]);
                    $commentsManager->updateStatus($comment);
                }
                // Compte le nombre de commentaires modérés pour adaptés l'affichage du message
                $nbselectedComments = count($_POST["selectedComments"]);
                if ($nbselectedComments>1) {
                    $session->setFlash($nbselectedComments . " commentaires ont été modérés.", "success");
                } else {
                    $session->setFlash("Le commentaire a été modéré.", "success");
                }
            }
        }
        
        // Enregistre le filtre
        if (isset($_POST["filter_status"]) && $_POST["filter_status"] >= 1) {
            $_SESSION["filter_status"] = htmlspecialchars($_POST["filter_status"]);
            $_SESSION["filter"] = "status = " . $_SESSION["filter_status"];
        }
    }

    if (!isset($_POST["filter_status"]) || (isset($_POST["filter_status"]) && empty($_POST["filter_status"]))) {
        $_SESSION["filter"] = "c.id > 0";
        $_SESSION["filter_status"] = NULL;
    }

    // Compte le nombre de commentaires
    $nbItems = $commentsManager->count($_SESSION["filter"]);

    // Vérifie l'ordre de tri par type
    if (!empty($_GET["orderBy"]) && ($_GET["orderBy"] == "content" || $_GET["orderBy"] == "user_name" || $_GET["orderBy"] == "status" || $_GET["orderBy"] == "report_date" || $_GET["orderBy"] == "nb_report" || $_GET["orderBy"] == "creation_date" )) {
        $orderBy = htmlspecialchars($_GET["orderBy"]);
    } else if (!empty($_COOKIE["orderBy"]["adminComments"])) {
        $orderBy = $_COOKIE["orderBy"]["adminComments"];
    } else {
        $orderBy = "creation_date";
    }
    // Vérifie l'ordre de tri si ascendant ou descendant
    if (!empty($_GET["order"]) && ($_GET["order"] == "desc" || $_GET["order"] == "asc")) {
        $order = htmlspecialchars($_GET["order"]);
    } else if (!empty($_COOKIE["order"]["adminComments"])) {
        $order = $_COOKIE["order"]["adminComments"];
    } else {
        $order = "desc";
    }
    // Si le tri par type vient de changer, alors le tri est toujours ascendant
    if (!empty($_COOKIE["order"]["adminComments"]) && $orderBy != $_COOKIE["orderBy"]["adminComments"]) {
        $order = "asc";
    }
    // Enregistre les tris en COOKIES
    setcookie("orderBy[adminComments]", $orderBy, time() + 365*24*3600, null, null, false, false);
    setcookie("order[adminComments]", $order, time() + 365*24*3600, null, null, false, false);

    // Initialise la pagination
    $linkNbDisplayed = "index.php?action=comments&orderBy=" . $orderBy . "&order=" . $order. "&";
    $pagination = new Pagination("adminComments", $nbItems, $linkNbDisplayed, $linkNbDisplayed, "#table-admin_comments");

    // Récupère les commentaires
    $comments = $commentsManager->getlist($_SESSION["filter"], $orderBy, $order,  $pagination->_nbLimit, $pagination->_nbDisplayed);
    
    require "view/backend/commentsView.php";
}