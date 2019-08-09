<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$usersManager = new UsersManager();
$db = $usersManager->db();

// Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
if (empty($_SESSION["userID"])) {
    header("Location: connection.php");
} else {
    // Récupère le rôle de l'utilisateur
    $userRole = $usersManager->getRole($_SESSION["userID"]);
    if ($userRole != 1) {
        header("Location: index.php");
    }
}

if (!empty($_POST)) {
    if (isset($_POST["moderation"])) {
       $moderation = 1; 
    } else {
        $moderation = 0; 
    }
    $req = $db->prepare("UPDATE settings SET blog_name = :blog_name, admin_email = :admin_email, default_role = :default_role, moderation = :moderation WHERE ID = 1 ");
    $req->execute(array(
        "blog_name" => htmlspecialchars($_POST["blog_name"]),
        "admin_email" => htmlspecialchars($_POST["admin_email"]),
        "default_role" => htmlspecialchars($_POST["default_role"]),
        "moderation" =>  $moderation
    ));
    $session->setFlash("Les paramètres ont été mis à jour.", "success");
}
    // Récupère les paramètres
    $req = $db->prepare("SELECT * FROM settings");
    $req->execute(array());
    $dataSettings = $req->fetch();   

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

    <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent">
                <li class="breadcrumb-item"><a href="index.php" class="text-blue">Accueil</a></li>
                <li class="breadcrumb-item active" aria-current="page">Administration</li>
            </ol>
    </nav>

        <div class="row">
            <div class="col-md-12 mx-auto mt-4">

                <h2 class="mb-4">Administration du site</h2>

            </div>
        </div>

        <?php $session->flash(); // Message en session flash ?>      

        <div class="row">
            
            <div class="col-md-6 mt-4">
                <form action="admin.php" method="post" class="col-md-12 card shadow">
                    <div class="form-group row">
                        <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Paramètres</h3>
                    </div>
                    <div class="form-group row">
                        <label for="blog_name" class="col-md-4 col-form-label">Titre du blog</label>
                        <div class="col-md-8">
                            <input type="text" name="blog_name" id="blog_name" class="form-control mb-4 shadow-sm" 
                                value="<?= $dataSettings["blog_name"] ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="admin_email" class="col-md-4 col-form-label">Adresse email</label>
                        <div class="col-md-8">
                            <input type="text" name="admin_email" id="admin_email" class="form-control mb-4 shadow-sm" 
                                value="<?= $dataSettings["admin_email"] ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="default_role" class="col-md-4 col-form-label">Rôle par défaut des utilisateurs</label>
                        <div class="col-md-8">
                            <select name="default_role" id="default_role" class="custom-select form-control shadow-sm">
                                <option value="1" <?= $dataSettings["default_role"] == 1 ? "selected" : "" ?>>Administrateur</option>
                                <option value="2" <?= $dataSettings["default_role"] == 2 ? "selected" : "" ?>>Editeur</option>
                                <option value="3" <?= $dataSettings["default_role"] == 3 ? "selected" : "" ?>>Auteur</option>
                                <option value="4" <?= $dataSettings["default_role"] == 4 ? "selected" : "" ?>>Contributeur</option>
                                <option value="5" <?= $dataSettings["default_role"] == 5 ? "selected" : "" ?>>Abonné</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="moderation" class="col-md-4 col-form-label">Modération</label>
                        <div class="col-md-1">
                            <input type="checkbox" name="moderation" id="moderation" class="form-control mb-4" 
                                value="true" <?= $dataSettings["moderation"] == 1 ? "checked" : "" ?>>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <input type="submit" name="validation" value="Valider" id="validation" class="btn btn-blue shadow">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="offset-md-2 col-md-4 offset-lg-3 col-lg-3 mt-4">
                <div class="list-group shadow">
                <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Navigation</h3>
                    <a href="admin_posts.php" class="list-group-item list-group-item-action text-blue">Gestion des articles</a>
                    <a href="admin_comments.php" class="list-group-item list-group-item-action text-blue">Gestion des commentaires</a>
                    <a href="admin_users.php" class="list-group-item list-group-item-action text-blue">Gestion des utilisateurs</a>
                    <a href="user_new.php" class="list-group-item list-group-item-action text-blue">Ajouter un utilisateur</a>
                </div>
            </div>

        </div>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>