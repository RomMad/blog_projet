<?php
// Si recherche, enregistre le filtre
if (!empty($POST)) {
    if (!empty($_POST["search"])) {
        header("Location : index.php");
    };
};

?>

<header id="header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark my-3 py-3 shadow">
        <a class="navbar-brand text-info" href="index.php">Jean Forteroche | Le blog</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php"><span class="fas fa-home"></span> Accueil <span class="sr-only">(current)</span></a>
                </li>
                <?php 
                
                if (isset($_SESSION["userRole"]) && $_SESSION["userRole"]<5) {
                ?> 
                <li class="nav-item">
                    <a class="nav-link" href="edit_post.php?type=1">Nouvel article</a>
                </li>
                <?php 
                };

                if (isset($_SESSION["userRole"]) && $_SESSION["userRole"]==1) {
                ?> 
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="admin.php" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Admin
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="admin.php">Administration générale</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="admin_posts.php">Gestion des articles</a>
                        <a class="dropdown-item" href="admin_comments.php">Gestion des commentaires</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="admin_users.php">Gestion des utilisateurs</a>
                    </div>
                </li>
                <?php 
                };
                ?>
                <!-- <li class="nav-item">
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Page 3</a>
                </li> -->
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input for="search" type="search" class="form-control mr-sm-2" placeholder="Recherche" aria-label="Search">
                <button name="search" id="search" class="btn btn-outline-info my-2 my-sm-0" type="submit"><span class="fas fa-search"></span></button>
            </form>

            <div class="ml-3 text-light">
            <?php 
            if (isset($_SESSION["userID"])) {
            ?>
            <a class="text-info font-weight-bold" href="profil.php"><span class="fas fa-user"></span> <?= $_SESSION["userLogin"] ?></a>
            <br />
            <a class="text-info" href="deconnection.php">Vous déconnecter</a>
            <?php 
            } else {
                ?>
                <a class="text-info" href="connection.php">Se connecter</a>
                <br />
                <a class="text-info" href="inscription.php">S'inscrire</a>
            <?php
            };
            ?>
            </div>

        </div>
    </nav>
</header>