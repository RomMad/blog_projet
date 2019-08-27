<?php 
namespace controller\backend;

class ListUsersController {

    protected   $_session,
                $_usersManager,
                $_user,
                $_pagination;

    public function __construct($session) {
        $this->_session = $session;
        $this->_usersManager = new \model\UsersManager();
        $this->init();
    }

    protected function init() {
        // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
        if (!isset($_SESSION["user"])) {
            header("Location: connection"); 
            exit;
        } else {
            // Redirige vers la page d'erreur 403 si l'utilisateur n'a pas les droits
            if ($_SESSION["user"]["role"] != 1) {
                header("Location: error403"); 
                exit;
            }
        }

        if (!empty($_POST)) {
            if (!empty($_POST["action_apply"]) && isset($_POST["selectedUsers"])) {
                // Supprime les utilisateurs sélectionnés via une boucle
                if ($_POST["action_apply"] == "delete") {
                    foreach ($_POST["selectedUsers"] as $selectedUser) {
                        $user = $this->_usersManager->get($selectedUser);
                        $this->_usersManager->delete($user);
                        $this->_session->setFlash("L'utilisateur <b>" . $user->login() . "</b> a été supprimé.", "warning");
                    }
                }
            }

            $_SESSION["filter"] = "u.id > 0";
            // Si sélection d'un filtre 'rôle', enregistre le filtre
            if (!empty($_POST["filter_role"])) {
                $_SESSION["filter_role"] = htmlspecialchars($_POST["filter_role"]);
                $_SESSION["filter"] = "role = " . htmlspecialchars($_POST["filter_role"]);
            } else {
                $_SESSION["filter_role"] = NULL;
            }
            // Si recherche, enregistre le filtre
            if (!empty($_POST["search_user"])) {
                $_SESSION["search_user"] = htmlspecialchars($_POST["search_user"]);
                $_SESSION["filter"] = $_SESSION["filter"] . " AND (login LIKE '%" .  $_SESSION["search_user"] . "%' OR email LIKE '%" .  $_SESSION["search_user"] . "%' OR name LIKE '%" . $_SESSION["search_user"] . "%' OR surname LIKE '%"  . $_SESSION["search_user"] . "%')";
            }
        }

        if (empty($_POST) && !isset($_GET["order"])) {
            $_SESSION["filter_role"] = NULL;
            $_SESSION["search_user"] = "";
            $_SESSION["filter"] = "u.id > 0";
        }
        // Compte le nombre d'utilisateurs
        $nbItems = $this->_usersManager->count($_SESSION["filter"]);

        if (isset($_POST["filter"]) || isset($_POST["search_user"])) {
            $this->_session->setFlash($nbItems . " résultat(s).", "light");
        }

        // Vérifie l'ordre de tri par type
        if (!empty($_GET["orderBy"]) && ($_GET["orderBy"] == "login" || $_GET["orderBy"] == "name" || $_GET["orderBy"] == "surname" || $_GET["orderBy"] == "email" || $_GET["orderBy"] == "role" | $_GET["orderBy"] == "registration_date")) {
            $orderBy = htmlspecialchars($_GET["orderBy"]);
        } else if (!empty($_COOKIE["orderBy"]["adminUsers"])) {
            $orderBy = $_COOKIE["orderBy"]["adminUsers"];
        } else {
            $orderBy = "login";
        }
        // Vérifie l'ordre de tri si ascendant ou descendant
        if (!empty($_GET["order"]) && ($_GET["order"] == "desc" || $_GET["order"] == "asc")) {
            $order = htmlspecialchars($_GET["order"]);
        } else if (!empty($_COOKIE["order"]["adminUsers"])) {
            $order = $_COOKIE["order"]["adminUsers"];
        } else {
            $order = "desc";
        }
        // Si le tri par type vient de changer, alors le tri est toujours ascendant
        if (!empty($_COOKIE["order"]["adminUsers"]) && $orderBy != $_COOKIE["orderBy"]["adminUsers"]) {
            $order = "asc";
        }
        // Enregistre les tris en COOKIES
        setcookie("orderBy[adminUsers]", $orderBy, time() + 365*24*3600, null, null, FALSE, TRUE);
        setcookie("order[adminUsers]", $order, time() + 365*24*3600, null, null, FALSE, TRUE);

        // Initialise la pagination
        $linkNbDisplayed = "users-orderBy-" . $orderBy . "-order-" . $order;
        $this->_pagination = new \model\Pagination("adminUsers", $nbItems, $linkNbDisplayed, $linkNbDisplayed . "-", "#table-admin_users");

        // Récupère les utilisateurs
        $users = $this->_usersManager->getlist($_SESSION["filter"], $orderBy, $order,  $this->_pagination->_nbLimit, $this->_pagination->_nbDisplayed);
            
        require "view/backend/listUsersView.php";
    }
}