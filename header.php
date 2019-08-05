<header id="header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3 py-3 shadow">
        <a class="navbar-brand text-blue" href="index.php">Jean Forteroche | Le blog</a>
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
                    <a class="nav-link" href="postEdit.php?type=1">Nouvel article</a>
                </li>
                <?php 
                }

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
                        <a class="dropdown-item" href="user_new.php">Ajouter un utilisateur</a>
                    </div>
                </li>
                <?php 
                }
                ?>
                <!-- <li class="nav-item">
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Page 3</a>
                </li> -->
            </ul>
            <form action="blog.php" method="get" class="form-inline my-2 my-lg-0">
                <label for="search" class="sr-only col-form-label">Recherche</label>
                <input name="search" id="search" type="search" class="form-control mr-sm-2" placeholder="Recherche" aria-label="Search" 
                    value="<?= isset($_GET["search"]) ? htmlspecialchars($_GET["search"]) : "" ?>">
                <button id="search" class="btn btn-outline-blue my-2 my-sm-0" type="submit"><span class="fas fa-search"></span></button>
            </form>

            <div class="ml-3 text-light">
            <?php 
            if (isset($_SESSION["userID"])) {
            ?>
            <a class="text-blue font-weight-bold" href="profil.php"><span class="fas fa-user"></span> <?= $_SESSION["userLogin"] ?></a>
            <br />
            <a class="text-blue" href="deconnection.php">Vous déconnecter</a>
            <?php 
            } else {
                ?>
                <a class="text-blue" href="connection.php">Se connecter</a>
                <br />
                <a class="text-blue" href="inscription.php">S'inscrire</a>
            <?php
            }
            ?>
            </div>

        </div>
    </nav>
</header>