<?php 
    session_start();

    include("connection_bdd.php");
    // Redirige vers la page de connexion si non connecté
    if (empty($_SESSION)) {
        header('Location: connection.php');
    };

    var_dump($_GET);
        if (!empty($_GET)) {
        $req = $bdd->prepare('SELECT ID, user_login, user_name, user_surname, user_email, user_status, DATE_FORMAT(user_birthday, \'%d/%m/%Y %H:%i\') AS user_birthday_fr FROM users WHERE ID =?');

        $req->execute(array($_SESSION['ID']));
        $data = $req->fetch();

        $user_login = $data['user_login'];
        $user_name = $data['user_name'];
        $user_surname = $data['user_surname'];
        $user_birthday = $data['user_birthday_fr'];
        $user_email = $data['user_email'];
        $user_status = $data['user_status'];
    };

    // Met à jour le profil
    var_dump($_POST);
    // Vérification si informations dans variable POST
    if (!empty($_POST)) {
        $user_login = htmlspecialchars($_POST['user_login']);
        $user_name = htmlspecialchars($_POST['user_name']);
        $user_surname = htmlspecialchars($_POST['user_surname']);
        $user_email = htmlspecialchars($_POST['user_email']);
        $user_status = htmlspecialchars($_POST['user_status']);
        $user_pass = htmlspecialchars($_POST['user_pass']);

        // Récupération de l'utilisateur et de son pass hashé
        $req = $bdd->prepare('SELECT ID, user_pass FROM users WHERE ID = ?');
        $req->execute(array($_SESSION['ID']));
        $resultat = $req->fetch();

        // Comparaison du pass envoyé via le formulaire avec la base
        $isPasswordCorrect = password_verify($_POST['user_pass'], $resultat['user_pass']);
        
        // Vérifie si Login et Password existent
        if (!empty($_POST)) {
            if (!$resultat) {
            $statusProfil = "Mot de passe incorrect.";
            } else {
                if ($isPasswordCorrect) {
                    $req = $bdd->prepare('UPDATE users SET user_login = :new_user_login, user_name = :new_user_name, user_surname = :new_user_surname, user_email = :new_user_email, user_status = :new_user_status, user_date_update = NOW() 
                    WHERE ID = :ID');
                    $req->execute(array(
                        'new_user_login' => $user_login,
                        'new_user_name' => $user_name,
                        'new_user_surname' => $user_surname,
                        'new_user_email' => $user_email,
                        'new_user_status' => $user_status,
                        'ID' => $_SESSION['ID']
                        )); 
                    
                    $_SESSION['user_login'] = $user_login;

                    $statusProfil = "Profil mis à jour.";
                } else {
                    $statusProfil = "Mot de passe incorrect.";
                };
            };
        };
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

                <?php  
            if (isset($statusProfil)) {
                echo $statusProfil;
            ?> <br /> <br />
                <?php 
            }; 
        ?>
                <form action="profil.php" method="post" class="col-md-12 card shadow">
                    <div class="form-group row">
                        <h2 class="card-header col-md-12 h2 bg-light text-dark">Profil</h2>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="user_login" class="col-md-4 col-form-label">Login</label>
                                <div class="col-md-8">
                                    <input type="text" name="user_login" id="user_login" class="form-control"
                                        value="<?= isset($user_login) ? $user_login : '' ?>"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_name" class="col-md-4 col-form-label">Nom</label>
                                <div class="col-md-8">
                                    <input type="text" name="user_name" id="user_name" class="form-control"
                                        value="<?= isset($user_name) ? $user_name : '' ?>"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_surname" class="col-md-4 col-form-label">Prénom</label>
                                <div class="col-md-8">
                                    <input type="text" name="user_surname" id="user_surname" class="form-control"
                                        value="<?= isset($user_surname) ? $user_surname : '' ?>"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_email" class="col-md-4 col-form-label">Adresse email</label>
                                <div class="col-md-8">
                                    <input type="text" name="user_email" id="user_email" class="form-control"
                                        value="<?= isset($user_email) ? $user_email : '' ?>"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_email" class="col-md-4 col-form-label">Date de naissance</label>
                                <div class="col-md-5">
                                    <input type="date" name="user_birthday" id="user_birthday" class="form-control"
                                        value="<?= isset($user_birthday) ? $user_birthday : '' ?>"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_status" class="col-md-4 col-form-label">Type de profil</label>
                                <div class="col-md-5">
                                    <input type="text" name="user_status" id="user_status" class="form-control"
                                        value="<?= isset($user_status) ? $user_status : '' ?>"><br />
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <label for="user_pass" class="col-md-4 col-form-label">Mot de passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="user_pass" id="user_pass" class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_pass-confirm" class="col-md-4 col-form-label">Confirmation mot de
                                    passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="user_pass-confirm" id="user_pass-confirm"
                                        class="form-control"><br />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <input type="submit" value="Mettre à jour" id="validation"
                                            class="btn btn-info shadow">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <form action="profil.php" method="post" class="col-md-12 card shadow">
                    <div class="form-group row">
                        <h2 class="card-header h4 col-md-12 h2 bg-light text-dark">Mise à jour du mot de passe</h2>
                    </div>
                    <div class="row">
                                <label for="user_pass" class="col-md-4 col-form-label">Ancien mot de passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="user_pass" id="user_pass" class="form-control"><br />
                                </div>
                            </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="new_pass" class="col-md-4 col-form-label">Nouveau mot de passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="new_pass" id="new_pass" class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="new_pass_confirm" class="col-md-4 col-form-label">Confirmation nouveau mot de
                                    passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="new_pass_confirm" id="new_pass_confirm"
                                        class="form-control"><br />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <input type="submit" value="Mettre à jour" id="validation"
                                            class="btn btn-info shadow">
                                    </div>
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