<?php 

function users() {
    
    spl_autoload_register("loadClass");

$session = new Session();
$usersManager = new UsersManager();

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
    if (!empty($_POST["action_apply"]) && isset($_POST["selectedUsers"])) {
        // Supprime les utilisateurs sélectionnés via une boucle
        if ($_POST["action_apply"] == "delete") {
            foreach ($_POST["selectedUsers"] as $selectedUser) {
                $user = $usersManager->get($selectedUser);
                $usersManager->delete($user);
                $session->setFlash("L'utilisateur <b>" . $user->login() . "</b> a été supprimé.", "warning");
            }
        }
    }
    // Si sélection d'un filtre 'rôle', enregistre le filtre
    if (!empty($_POST["filter_role"])) {
        $_SESSION["filter_role"] = $_POST["filter_role"];
        $_SESSION["filter"] = "role = " . htmlspecialchars($_POST["filter_role"]);
    }
    // Si recherche, enregistre le filtre
    if (!empty($_POST["filter_search"])) {
        $_SESSION["filter_search"] = htmlspecialchars($_POST["search_user"]);
        $_SESSION["filter"] = "login LIKE '%" .  $_SESSION["filter_search"] . "%' OR email LIKE '%" .  $_SESSION["filter_search"] . "%' OR name LIKE '%" . $_SESSION["filter_search"] . "%' OR surname LIKE '%"  . $_SESSION["filter_search"] . "%'";
    }
}

if (!isset($_POST["filter_search"]) && !isset($_POST["filter_role"]) || (isset($_POST["filter_role"]) && empty($_POST["filter_role"]))) {
    $_SESSION["filter"] = "u.id > 0";
    $_SESSION["filter_role"] = NULL;
    $_SESSION["filter_search"] = "";
}

// Compte le nombre d'utilisateurs
$nbItems = $usersManager->count($_SESSION["filter"]);

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
setcookie("orderBy[adminUsers]", $orderBy, time() + 365*24*3600, null, null, false, true);
setcookie("order[adminUsers]", $order, time() + 365*24*3600, null, null, false, true);

// Initialise la pagination
$linkNbDisplayed = "index.php?action=users&orderBy=" . $orderBy . "&order=" . $order. "&";
$pagination = new Pagination("adminUsers", $nbItems, $linkNbDisplayed, $linkNbDisplayed, "#table-admin_users");

// Récupère les utilisateurs
$users = $usersManager->getlist($_SESSION["filter"], $orderBy, $order,  $pagination->_nbLimit, $pagination->_nbDisplayed);
    
require "view/backend/usersView.php";
}