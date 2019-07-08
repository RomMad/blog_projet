<?php 
    session_start();

    include("connection_bdd.php");

    var_dump($_POST);
    // Vérifie si informations dans variable POST
    if (!empty($_POST)) {
        $user_login = htmlspecialchars($_POST['login']);
        $user_name = htmlspecialchars($_POST['name']);
        $user_surname = htmlspecialchars($_POST['surname']);
        $user_email = htmlspecialchars($_POST['email']);
        $user_birthdate = !empty($_POST['birthdate']) ? htmlspecialchars($_POST['birthdate']) : NULL;
        $user_pass_hash = password_hash(htmlspecialchars($_POST['pass']), PASSWORD_DEFAULT); // Hachage du mot de passe
        // Insert les données dans la table users
        $req = $bdd->prepare('INSERT INTO users(user_login, user_email, user_name, user_surname, user_birthdate, user_pass) VALUES(:user_login, :user_email, :user_name, :user_surname, :user_birthdate, :user_pass)');
        $req->execute(array(
            'user_login' => $user_login,
            'user_email' => $user_email,
            'user_name' => $user_name,
            'user_surname' => $user_surname,
            'user_birthdate' => $user_birthdate,
            'user_pass' => $user_pass_hash,
            ));
            // Récupère l'ID de l'utilisateur
            $req = $bdd->prepare('SELECT ID FROM users WHERE user_login = ?');
            $req->execute(array($user_login));
            $data = $req->fetch();
            // Ajoute les infos de l'utilisateurs dans la Session
            $_SESSION['ID'] = $data['ID'];
            $_SESSION['user_login'] = $user_login;
            $statusInscription = "Inscription réussie.";
            ?> 
            <meta http-equiv="refresh" content="2;url=index.php"/>
            <?php 
    };
?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="inscription" class="row">

            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">

            <?= isset($statusInscription) ? $statusInscription : '' ?>
                <br /> <br />
                <form action="inscription.php" method="post" class="col-md-12 card shadow">
                    <div class="form-group row">
                        <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Inscription</h3>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="login" class="col-md-4 col-form-label">Login</label>
                                <div class="col-md-8">
                                    <input type="text" name="login" id="login" 
                                        class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                                <div class="col-md-8">
                                    <input type="email" name="email" id="email" 
                                        class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="name" class="col-md-4 col-form-label">Nom</label>
                                <div class="col-md-8">
                                    <input type="text" name="name" id="name" class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                                <div class="col-md-8">
                                    <input type="text" name="surname" id="surname" class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="birthdate" class="col-md-4 col-form-label">Date de naissance</label>
                                <div class="col-md-5">
                                    <input type="date" name="birthdate" id="birthdate" class="form-control"><br />
                                </div>
                            </div>                            
                            <div class="row">
                                <label for="pass" class="col-md-4 col-form-label">Mot de passe</label>
                                <div class="col-md-8">
                                    <input type="password" name="pass" id="pass" 
                                        class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="pass-confirm" class="col-md-4 col-form-label">Confirmation mot de
                                    passe</label>
                                <div class="col-md-8">
                                    <input type="password" name="pass-confirm" id="pass-confirm" 
                                        class="form-control"><br />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <input type="submit" value="Valider" id="validation" class="btn btn-info shadow">
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
            </div>

        </section>

    </div>

    <?php include("scripts.html"); ?>

</html