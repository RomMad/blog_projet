<header id="header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
        <a class="navbar-brand text-info" href="#">Blog</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php"><span class="fas fa-home"></span> Accueil <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="blog.php">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="edit_post.php?type=1">Nouvel article</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="connection.php">Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="inscription.php">Inscription</a>
                </li>

        <?php 
            if (isset($_SESSION["user_status"]) && $_SESSION["user_status"]==0) {
        ?> 
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="admin.php" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Admin
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="admin.php">Administration générale</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="admin-posts.php">Gestion des articles</a>
                    <a class="dropdown-item" href="admin-comments.php">Gestion des commentaires</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="admin-users.php">Gestion des utilisateurs</a>
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
                <input class="form-control mr-sm-2" type="search" placeholder="Recherche" aria-label="Search">
                <button class="btn btn-outline-info my-2 my-sm-0" type="submit"><span class="fas fa-search"></span></button>
            </form>

            <div class="ml-3 text-light">
            <?php 
            if (isset($_SESSION["user_ID"])) {
            ?>
            <a class="text-info font-weight-bold" href="profil.php"><span class="fas fa-user"></span> <?= $_SESSION["user_login"] ?></a>
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