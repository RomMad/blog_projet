<?php 
    session_start();

    require("connection_bdd.php");
    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["userID"])) {
        header("Location: index.php");
    } else {
        // Récupère les informations de l'utilisateur
        $req = $bdd->prepare("SELECT role FROM users WHERE ID =?");
        $req->execute(array($_SESSION["userID"]));
        $userRole = $req->fetch();
        
        if ($userRole["role"]!=1) {
            header("Location: index.php");
        };
    };

    $filter = "u.ID > 0";

    var_dump($_POST);
    if (!empty($_POST)) {
        if (!empty($_POST["action_apply"]) && isset($_POST["selectedUsers"])) {
            // Supprime les utilisateurs sélectionnés via une boucle
            if ($_POST["action_apply"] == "delete") {
                foreach ($_POST["selectedUsers"] as $selectedUser) {
                    $req = $bdd->prepare("DELETE FROM users WHERE ID = ? ");
                    $req->execute(array($selectedUser));
                };
                // Compte le nombre d'utilisateurs supprimés pour adaptés l'affichage du message
                $nbselectedUsers = count($_POST["selectedUsers"]);
                if ($nbselectedUsers>1) {
                    $msgAdmin = $nbselectedUsers . " utilisateurs ont été supprimés.";
                } else {
                    $msgAdmin = "L'utilisateur a été supprimé.";
                };
                $typeAlert = "warning"; 
            };
            // Modère les utilisateurs sélectionnés via une boucle
            if ($_POST["action_apply"] == "moderate") {
                foreach ($_POST["selectedUsers"] as $selectedUser) {
                    $req = $bdd->prepare("UPDATE users SET role = 1 WHERE ID = ? ");
                    $req->execute(array($selectedUser));
                };
                // Compte le nombre d'utilisateurs modérés pour adaptés l'affichage du message
                $nbselectedUsers = count($_POST["selectedUsers"]);
                if ($nbselectedUsers>1) {
                    $msgAdmin = $nbselectedUsers . " utilisateurs ont été modérés.";
                } else {
                    $msgAdmin = "L'utilisateur a été modéré.";
                };
                $typeAlert = "success"; 
            };
            $_SESSION["flash"] = array(
                "msg" => $msgAdmin,
                "type" =>  $typeAlert
            );
        };
        // Si sélection d'un filtre 'rôle', enregistre le filtre
        if (!empty($_POST["filter_role"])) {
            $filter = "role = " . htmlspecialchars($_POST["filter_role"]);
        };
        // Si recherche, enregistre le filtre
        if (!empty($_POST["filter_search"])) {
            $search = htmlspecialchars($_POST["search_user"]);
            $filter = "login LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' OR name LIKE '%" . $search . "%' OR surname LIKE '%"  . $search . "%'";
        };
    };

    // Compte le nombre d'utilisateurs
    $req = $bdd->prepare("SELECT COUNT(*) AS nb_Users, u.role, r.ID 
    FROM users u
    LEFT JOIN user_role r
    ON u.role = r.ID  
    WHERE $filter
    ");
    $req->execute(array());
    $nbUsers = $req->fetch();
    $nbItems = $nbUsers["nb_Users"];

    // Vérification si informations dans variable POST
    if (!empty($_POST["nbDisplayed"])) {
        $nbDisplayed =  htmlspecialchars($_POST["nbDisplayed"]);
        setcookie("adminNbDisplayedUsers", $nbDisplayed, time() + 365*24*3600, null, null, false, true);
    } else if (!empty($_COOKIE["adminNbDisplayedUsers"])) {
        $nbDisplayed = $_COOKIE["adminNbDisplayedUsers"];
    } else {
        $nbDisplayed = 20;
    };
    var_dump($_GET);  
    // Vérifie l'ordre de tri par type
    if (!empty($_GET["orderBy"]) && ($_GET["orderBy"] == "login" || $_GET["orderBy"] == "name" || $_GET["orderBy"] == "surname" || $_GET["orderBy"] == "email" || $_GET["orderBy"] == "role" | $_GET["orderBy"] == "registration_date_fr")) {
        $orderBy = htmlspecialchars($_GET["orderBy"]);
    } else if (!empty($_COOKIE["adminUsersOrderBy"])) {
        $orderBy = $_COOKIE["adminUsersOrderBy"];
    } else {
        $orderBy = "login";
    };
    // Vérifie l'ordre de tri si ascendant ou descendant
    if (!empty($_GET["order"]) && ($_GET["order"] == "desc" || $_GET["order"] == "asc")) {
        $order = htmlspecialchars($_GET["order"]);
    } else if (!empty($_COOKIE["adminUsersOrder"])) {
        $order = $_COOKIE["adminUsersOrder"];
    } else {
        $order = "desc";
    };
    // Si le tri par type vient de changer, alors le tri est toujours ascendant
    if (!empty($_COOKIE["adminUsersOrder"]) && $orderBy != $_COOKIE["adminUsersOrderBy"]) {
        $order = "asc";
    };
    // Enregistre les tris en COOKIES
    setcookie("adminUsersOrderBy", $orderBy, time() + 365*24*3600, null, null, false, true);
    setcookie("adminUsersOrder", $order, time() + 365*24*3600, null, null, false, true);

    // Vérification si informations dans variable GET
    if (!empty($_GET["page"])) {
        $page = htmlspecialchars($_GET["page"]);
        // Calcul le nombre de pages par rapport aux nombre d'utilisateurs
        $maxUser =  $page*$nbDisplayed;
        $minUser = $maxUser-$nbDisplayed;
    } else  {
        $page = 1;
        $minUser = 0;
        $maxUser = $nbDisplayed;
    };
    
    // Initialisation des variables pour la pagination
    $linkNbDisplayed = "admin_Users.php?orderBy=" . $orderBy . "&order=" . $order. "&";
    $linkPagination = "admin_Users.php?orderBy=" . $orderBy . "&order=" . $order. "&";
    $anchorPagination = "#table-admin_Users";
    $nbPages = ceil($nbItems / $nbDisplayed);
    require("pagination.php");

    // Récupère les utilisateurs
    $req = $bdd->prepare("SELECT u.ID, u.login, u.name, u.surname, u.email, r.role_user, 
    DATE_FORMAT(u.registration_date, \"%d/%m/%Y %H:%i\") AS registration_date_fr, 
    DATE_FORMAT(u.update_date, \"%d/%m/%Y %H:%i\") AS update_date_fr 
    FROM users u
    LEFT JOIN user_role r
    ON u.role = r.ID
    WHERE $filter 
    ORDER BY $orderBy $order
    LIMIT  $minUser, $maxUser");
    $req->execute(array());

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
            <section id="table-admin_Users" class="col-md-12 mx-auto mt-4 table-admin">

                <h2 class="mb-4">Gestion des utilisateurs
                    <span class="badge badge-secondary font-weight-normal"><?= $nbUsers["nb_Users"] ?> </span>
                </h2>
                
                <?php include("msg_session_flash.php") ?>

                <?php 
                // Affiche les résultats si recherche
                if (isset($_POST["filter"]) || isset($_POST["filter_search"])) {
                    echo "<p> " . $nbItems . " résultat(s).</p>";
                };    
                ?>

                <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

                <form action="<?= $linkNbDisplayed ?>" method="post" class="">
                    <div class="row">
                    
                        <div class="col-md-4 form-inline mb-2 pr-md-2">
                            <label class="sr-only col-form-label px-2 py-2" for="action">Action</label>
                                <select name="action_apply" id="action_apply" class="custom-select form-control shadow" value="Par auteur">
                                    <option value="">-- Action --</option>
                                    <option value="delete">Supprimer</option>
                                </select>
                            <input type="submit" id="apply" name="apply" alt="Appliquer" class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" 
                                value="OK" onclick="if(window.confirm('Confirmer l\'action ?')){return true;}else{return false;}">
                        </div>

                        <div class="col-md-4 form-inline mx-md-0 mb-2 pr-md-2">
                            <label class="sr-only col-form-label px-2 py-2" for="filter_role">Filtre</label>
                                <select name="filter_role" id="filter_role" class="custom-select form-control shadow" value="Par auteur">
                                    <option value="">-- Rôle --</option>
                                    <option value="1">Administrateur</option>
                                    <option value="2">Editeur</option>
                                    <option value="3">Auteur</option>
                                    <option value="4">Contributeur</option>
                                    <option value="5">Abonné</option>
                                </select>
                            <input type="submit" id="filter" name="filter" alt="Filtrer" class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="Filtrer">
                        </div>
                        <div class="col-md-4 form-inline mx-md-0 mb-2 px-md-2">
                                <label for="search_user"class="sr-only col-form-label px-2 py-2">Recherche</label>
                                <input type="search" name="search_user" id="search_user" class="form-control px-md-1 shadow" placeholder="Recherche" aria-label="Search" 
                                    value="<?= isset($_POST["search_user"]) ? htmlspecialchars($_POST["search_user"]) : "" ?>">
                                <input type="submit" id="filter_search" name="filter_search" alt="filter_search" class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="OK">
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered table-striped table-hover shadow">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col" class="align-middle">
                                            <input type="checkbox" name="allselectedUsers" id="all-checkbox" />
                                            <label for="allselectedUsers" class="sr-only">Tout sélectionner</label>
                                        </th>
                                        <th scope="col" class="align-middle">
                                            <a href="admin_Users?orderBy=login&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Login
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
                                            <a href="admin_Users?orderBy=name&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Nom
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
                                            <a href="admin_Users?orderBy=surname&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Prénom
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
                                            <a href="admin_Users?orderBy=email&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Email
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
                                            <a href="admin_Users?orderBy=role&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Rôle
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
                                            <a href="admin_Users?orderBy=registration_date_fr&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date d'enregistrement
                                            <?php 
                                            if ($orderBy == "registration_date_fr") {
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
                                    while ($dataUsers=$req->fetch()) {
                                    ?>
                                        <tr>
                                            <th scope="row">
                                                <input type="checkbox" name="selectedUsers[]" id="User<?= $dataUsers["ID"] ?>" value="<?= $dataUsers["ID"] ?>" class=""/>
                                                <label for="selectedUsers[]" class="sr-only">Sélectionner</label>
                                            </th>
                                            <td><?= $dataUsers["login"] ?></td>
                                            <td><?= $dataUsers["name"] ?></td>
                                            <td><?= $dataUsers["surname"] ?></td>
                                            <td><?= $dataUsers["email"] ?></td>
                                            <td><?= $dataUsers["role_user"] ?></td>
                                            <td><?= $dataUsers["registration_date_fr"] ?></td>
                                        </tr>
                                    <?php
                                    };
                                    ?>
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </form>

                <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->
                
            </section>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>