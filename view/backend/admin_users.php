<?php 
function loadClass($classname) {
    require $classname . ".php";
}

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

if (empty($_GET)) {
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
$linkNbDisplayed = "admin_users.php?orderBy=" . $orderBy . "&order=" . $order. "&";
$pagination = new Pagination("adminUsers", $nbItems, $linkNbDisplayed, $linkNbDisplayed, "#table-admin_users");

// Récupère les utilisateurs
$users = $usersManager->getlist($_SESSION["filter"], $orderBy, $order,  $pagination->_nbLimit, $pagination->_nbDisplayed);

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

    <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-blue">Accueil</a></li>
                <li class="breadcrumb-item"><a href="admin.php" class="text-blue">Administration</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gestion des utilisateurs</li>
            </ol>
    </nav>

        <div class="row">
            <section id="table-admin_users" class="col-md-12 mx-auto mt-4 table-admin">

                <h2 class="mb-4">Gestion des utilisateurs
                    <span class="badge badge-secondary font-weight-normal"><?= $nbItems ?> </span>
                </h2>
                
                <?php 
                $session->flash(); // Message en session flash

                // Affiche les résultats si recherche
                if (isset($_POST["filter"]) || isset($_POST["filter_search"])) {
                    echo "<p> " . $nbItems . " résultat(s).</p>";
                }    
                ?>

                <form action="<?= $linkNbDisplayed ?>" method="post" class="">
                    <div class="row">
                    
                        <div class="col-md-4 form-inline mb-2 px-lg-3">
                            <label class="sr-only col-form-label" for="action">Action</label>
                                <select name="action_apply" id="action_apply" class="custom-select form-control mr-1 shadow" value="Par auteur">
                                    <option value="">-- Action --</option>
                                    <option value="delete">Supprimer</option>
                                </select>
                            <input type="submit" id="apply" name="apply" alt="Appliquer" class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" 
                                value="OK" onclick="if(window.confirm('Confirmer l\'action ?')){return true;}else{return false;}">
                        </div>

                        <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                            <label class="sr-only col-form-label" for="filter_role">Filtre</label>
                                <select name="filter_role" id="filter_role" class="custom-select form-control mr-1 shadow" value="Par auteur">
                                    <option <?= $_SESSION["filter_role"] == NULL ? "selected" : "" ?> value="">-- Rôle --</option>
                                    <option <?= $_SESSION["filter_role"] == 1 ? "selected" : "" ?> value="1">Administrateur</option>
                                    <option <?= $_SESSION["filter_role"] == 2 ? "selected" : "" ?> value="2">Editeur</option>
                                    <option <?= $_SESSION["filter_role"] == 3 ? "selected" : "" ?> value="3">Auteur</option>
                                    <option <?= $_SESSION["filter_role"] == 4 ? "selected" : "" ?> value="4">Contributeur</option>
                                    <option <?= $_SESSION["filter_role"] == 5 ? "selected" : "" ?> value="5">Abonné</option>
                                </select>
                            <input type="submit" id="filter" name="filter" alt="Filtrer" class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="Filtrer">
                        </div>
                        <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                                <label for="search_user"class="sr-only col-form-label">Recherche</label>
                                <input type="search" name="search_user" id="search_user" class="form-control px-md-1 mr-1 shadow" placeholder="Recherche" aria-label="Search" 
                                    value="<?= $_SESSION["filter_search"] ?>">
                                <input type="submit" id="filter_search" name="filter_search" alt="filter_search" class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="OK">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered table-striped table-hover shadow">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col" class="align-middle">
                                            <input type="checkbox" name="allselectedUsers" id="all-checkbox"/>
                                            <label for="allselectedUsers" class="sr-only">Tout sélectionner</label>
                                        </th>
                                        <th scope="col" class="align-middle">
                                            <a href="admin_users?orderBy=login&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Login
                                            <?php 
                                            if ($orderBy == "login") {
                                            ?>
                                                <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                            <?php   
                                            }
                                            ?>
                                            </a>
                                        </th>
                                        <th scope="col" class="align-middle">
                                            <a href="admin_users?orderBy=name&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Nom
                                            <?php 
                                            if ($orderBy == "name") {
                                            ?>
                                                <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                            <?php   
                                            }
                                            ?>
                                            </a>
                                        </th>
                                        <th scope="col" class="align-middle">
                                            <a href="admin_users?orderBy=surname&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Prénom
                                            <?php 
                                            if ($orderBy == "surname") {
                                            ?>
                                                <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                            <?php   
                                            }
                                            ?>
                                            </a>
                                        </th>
                                        <th scope="col" class="align-middle">
                                            <a href="admin_users?orderBy=email&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Email
                                            <?php 
                                            if ($orderBy == "email") {
                                            ?>
                                                <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                            <?php   
                                            }
                                            ?>
                                            </a>
                                        </th>
                                        <th scope="col" class="align-middle">
                                            <a href="admin_users?orderBy=role&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Rôle
                                            <?php 
                                            if ($orderBy == "role") {
                                            ?>
                                                <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                            <?php   
                                            }
                                            ?>
                                            </a>
                                        </th>
                                        <th scope="col" class="align-middle">
                                            <a href="admin_users?orderBy=registration_date&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date d'enregistrement
                                            <?php 
                                            if ($orderBy == "registration_date") {
                                            ?>
                                                <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                            <?php   
                                            }
                                            ?>
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                <?php
                                if ($nbItems) {
                                    foreach ($users as $user) {
                                ?>
                                        <tr>
                                            <th scope="row">
                                                <input type="checkbox" name="selectedUsers[]" id="User<?= $user->id() ?>" value="<?= $user->id() ?>" class=""/>
                                                <label for="selectedUsers[]" class="sr-only">Sélectionner</label>
                                            </th>
                                            <td><?= $user->login() ?></td>
                                            <td><?= $user->name() ?></td>
                                            <td><?= $user->surname() ?></td>
                                            <td><?= $user->email() ?></td>
                                            <td><?= $user->role_user() ?></td>
                                            <td><?= $user->registration_date("") ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </form>

                <?php $pagination->view(); ?> <!-- Ajoute la barre de pagination -->
                
            </section>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>