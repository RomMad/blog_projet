<?php 
namespace controller\backend;

class ListCommentsController {

    protected   $_session,
                $_commentsManager,
                $_user,
                $_pagination;
                
    public function __construct($session) {
        $this->_session = $session;
        $this->_commentsManager = new \model\CommentsManager();
        $this->init();
    }

    protected function init() {
        // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
        if (!isset($_SESSION["user"])) {
            header("Location: connection"); 
            exit();
        } else {
            // Redirige vers la page d'erreur 403 si l'utilisateur n'a pas les droits
            if ($_SESSION["user"]["role"] >= 3) {
                header("Location: error403"); 
                exit();
            }
        }

        if (!empty($_POST)) {
            if (!empty($_POST["action_apply"]) && isset($_POST["selectedComments"])) {
                // Supprime les commentaires sélectionnés via une boucle
                if ($_POST["action_apply"] == "delete" && isset($_POST["selectedComments"])) {
                    foreach ($_POST["selectedComments"] as $selectedComment) {
                        $comment = new \model\Comments([
                            "id" => $selectedComment,
                        ]);
                        $this->_commentsManager->delete($comment);
                    }
                    // Compte le nombre de commentaires supprimés pour adaptés l'affichage du message
                    $nbselectedComments = count($_POST["selectedComments"]);
                    if ($nbselectedComments > 1) {
                        $this->_session->setFlash($nbselectedComments . " commentaires ont été supprimés.", "warning");
                    } else {
                        $this->_session->setFlash("Le commentaire a été supprimé.", "warning");
                    }
                }
                // Modère les commentaires sélectionnés via une boucle
                if ($_POST["action_apply"] == "moderate" && isset($_POST["selectedComments"])) {
                    foreach ($_POST["selectedComments"] as $selectedComment) {
                        $comment = new \model\Comments([
                            "id" => $selectedComment,
                            "status" => 2,
                            ]);
                        $this->_commentsManager->updateStatus($comment);
                    }
                    // Compte le nombre de commentaires modérés pour adaptés l'affichage du message
                    $nbselectedComments = count($_POST["selectedComments"]);
                    if ($nbselectedComments > 1) {
                        $this->_session->setFlash($nbselectedComments . " commentaires ont été modérés.", "success");
                    } else {
                        $this->_session->setFlash("Le commentaire a été modéré.", "success");
                    }
                }
            }
            
            // Enregistre le filtre
            if (isset($_POST["filter_status"]) && $_POST["filter_status"] >= 1) {
                $_SESSION["filter_status"] = htmlspecialchars($_POST["filter_status"]);
                $_SESSION["filter"] = "status = " . $_SESSION["filter_status"];

            }
        }

        if (empty($_POST) && !isset($_GET["order"])) {
            $_SESSION["filter_status"] = NULL;
            $_SESSION["filter"] = "c.id > 0";
        }

        // Compte le nombre de commentaires
        $nbItems = $this->_commentsManager->count($_SESSION["filter"]);

        if (isset($_POST["filter"])) {
            $this->_session->setFlash($nbItems . " résultat(s).", "light");
        }

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
        setcookie("orderBy[adminComments]", $orderBy, time() + 365*24*3600, NULL, NULL, FALSE, FALSE);
        setcookie("order[adminComments]", $order, time() + 365*24*3600, NULL, NULL, FALSE, FALSE);

        // Initialise la pagination
        $linkNbDisplayed = "comments-orderBy-" . $orderBy . "-order-" . $order;
        $this->_pagination = new \model\Pagination("adminComments", $nbItems, $linkNbDisplayed, $linkNbDisplayed . "-", "#table-admin_comments");

        // Récupère les commentaires
        $comments = $this->_commentsManager->getlist($_SESSION["filter"], $orderBy, $order,  $this->_pagination->_nbLimit, $this->_pagination->_nbDisplayed);

        require "view/backend/listCommentsView.php";
    }
}