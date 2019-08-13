<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$db = new Manager();
$db = $db->databaseConnection();
$usersManager = new UsersManager($db);
$settingsManager = new SettingsManager($db);

// Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
if (empty($_SESSION["userID"])) {
    header("Location: connection.php");
    exit;
} 

// Récupère le rôle de l'utilisateur
$userRole = $usersManager->getRole($_SESSION["userID"]);

if ($userRole != 1) {
    header("Location: index.php");
    exit;
}

if (!empty($_POST)) {
    $validation = true;
    $settings = new settings([
        "blog_name" => $_POST["blog_name"],
        "admin_email" => $_POST["admin_email"],
        "default_role" => $_POST["default_role"],
        "moderation" =>  isset($_POST["moderation"]) ? true : false,
        "posts_by_row" => $_POST["posts_by_row"],
    ]);
    // Vérifie si le nom du blog ne fait pas plus de 50 caractères
    if (iconv_strlen($settings->blog_name()) > 50) {
        $session->setFlash("Le nom du blog est trop long (maximum 50 caractères)", "danger");
        $validation = false;
    }
    // Vérifie si l'adresse email est correcte
    if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $settings->admin_email())) {
        $session->setFlash("L'adresse \"" .$settings->admin_email() . "\" est incorrecte.", "danger");
        $validation = false;
    }
    // Met à jour les données si validation est vrai
    if ($validation == true) {
        $settingsManager->update($settings);
        $session->setFlash("Les paramètres ont été mis à jour.", "success");
    }  
} else  {
// Récupère les paramètres
$settings = $settingsManager->get();
}

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
            <div class="col-md-12 mt-4">

                <h2 class="mb-4">Administration du site</h2>

            </div>
        </div>

        <?php $session->flash(); // Message en session flash ?>      

        <div class="row">
            
            <div class="col-md-8 col-lg-6 mt-4">
                <form action="admin.php" method="post" class="col-md-12 card shadow">
                    <div class="form-group row">
                        <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Paramètres</h3>
                    </div>
                    <div class="form-group row">
                        <label for="blog_name" class="col-md-4 col-form-label">Titre du blog</label>
                        <div class="col-md-8">
                            <input type="text" name="blog_name" id="blog_name" class="form-control mb-4 shadow-sm" 
                                value="<?= $settings->blog_name() ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="admin_email" class="col-md-4 col-form-label">Adresse email</label>
                        <div class="col-md-8">
                            <input type="text" name="admin_email" id="admin_email" class="form-control mb-4 shadow-sm" 
                                value="<?= $settings->admin_email() ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="default_role" class="col-md-4 col-form-label">Rôle par défaut des utilisateurs</label>
                        <div class="col-md-8">
                            <select name="default_role" id="default_role" class="custom-select form-control shadow-sm">
                                <option value="1" <?= $settings->default_role() == 1 ? "selected" : "" ?>>Administrateur</option>
                                <option value="2" <?= $settings->default_role() == 2 ? "selected" : "" ?>>Editeur</option>
                                <option value="3" <?= $settings->default_role() == 3 ? "selected" : "" ?>>Auteur</option>
                                <option value="4" <?= $settings->default_role() == 4 ? "selected" : "" ?>>Contributeur</option>
                                <option value="5" <?= $settings->default_role() == 5 ? "selected" : "" ?>>Abonné</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="posts_by_row" class="col-md-4 col-form-label">Nombre d'articles par rangée</label>
                        <div class="col-md-8">
                            <select name="posts_by_row" id="posts_by_row" class="custom-select form-control shadow-sm">
                                <option value="1" <?= $settings->posts_by_row() == 1 ? "selected" : "" ?>>1</option>
                                <option value="2" <?= $settings->posts_by_row() == 2 ? "selected" : "" ?>>2</option>
                                <option value="3" <?= $settings->posts_by_row() == 3 ? "selected" : "" ?>>3</option>
                                <option value="4" <?= $settings->posts_by_row() == 4 ? "selected" : "" ?>>4</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-4">Modération</div>
                        <div class="col-md-8">
                        <div class="form-check">
                            <input type="checkbox" name="moderation" id="moderation" class="form-check-input" value="true" <?= $settings->moderation() == 1 ? "checked" : "" ?>>
                            <label for="moderation" class="form-check-label sr-only">Modération</label>
                        </div>
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

            <div class="col-md-4 offset-lg-2 col-lg-4 mt-4">
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