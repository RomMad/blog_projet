<?php 

session_start();

require("connection_bdd.php");

var_dump($_GET);
var_dump($_POST);

// Vérifie si informations dans variables POST et GET
if (!empty($_POST) && isset($_GET["token"])) {
    $token = htmlspecialchars($_GET["token"]);
    $email = htmlspecialchars($_POST["email"]);
    $new_pass = htmlspecialchars($_POST["new_pass"]);
    $new_pass_confirm = htmlspecialchars($_POST["new_pass_confirm"]);

    $validation = true;
    $msgReset = "";
    $typeAlert = "danger";

    // Vérifie si le token est existe
    $req = $bdd->prepare("SELECT r.user_ID, r.reset_date, u.email
    FROM reset_passwords r
    LEFT JOIN users u
    ON r.user_ID = u.ID
    WHERE token = ? AND email = ?
    ");
    $req->execute(array(
        $token,
        $email
    ));
    $dataResetPassword = $req->fetch();
    
    // Calcule l'intervalle entre le moment de demande de réinitialisation et maintenant
    $dateNow = new DateTime("now", timezone_open("Europe/Paris"));
    $dateResetPassword = new DateTime($dataResetPassword["reset_date"], timezone_open("Europe/Paris"));
    $interval = date_timestamp_get($dateNow)-date_timestamp_get($dateResetPassword);
    $delay = 15*60; // 15 minutes x 60 secondes = 900 secondes

    // Vérifie si le token ou l'adresse email sont corrects
    if (!$dataResetPassword) {
        $msgReset = $msgReset . "<li>Le lien de réinitialisation ou l'adresse email sont incorrects.</li>";
        $validation = false;
    };
    //  Vérifie si la demande de réinitialisation est inférieure à 15 minutes
    if ($interval>$delay) {
        $msgReset = $msgReset . "<li>Le lien de réinitialisation est périmé.</li>";
        $validation = false;
    };
    // Vérifie si le nouveau mot de passe est valide (minimum 6 caratères, 1 lettre minuscule, 1 lettre majuscule, 1 chiffre)
    if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$#", $new_pass)) {
        $msgReset = $msgReset . "<li>Le nouveau mot de passe n'est pas valide.</li>";
        $validation = false;
    };
    // Vérifie si la confirmation du mot de passe est identique
    if ($new_pass!=$new_pass_confirm) {
        $msgReset = $msgReset . "<li>Le mot de passe et la confirmation sont différents.</li>";
        $validation = false;
    };
    // Met à jour le mot de passe si validation est vraie
    if ($validation) {        
    $new_pass_hash = password_hash($new_pass, PASSWORD_DEFAULT); // Hachage du mot de passe
    $req = $bdd->prepare("UPDATE users SET pass = :new_pass WHERE ID = :ID");                
    $req->execute(array(
        "new_pass" => $new_pass_hash,
        "ID" => $_SESSION["user_ID"]
        )); 

    $msgReset = "Le mot de passe a été modifié.";
    $typeAlert = "success";
    };

    $_SESSION["flash"] = array(
        "msg" => $msgReset,
        "type" =>  $typeAlert
    );
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">
        <section id="reset-password" class="row">
                <form action="reset_password.php?token=<?= isset($_GET["token"]) ? htmlspecialchars($_GET["token"]) : "" ?>" method="post" class="form-signin col-xs-8 col-sm-6 col-md-4 mx-auto mt-4 mb-4">
                    <h1 class="h3 mb-4 font-weight-normal text-center">Réinitialisation du mot de passe</h1>
                    <label for="email" class="sr-only">Email</label>
                    <input type="text" name="email" id="email" class="form-control mb-4" placeholder="Email">
                    <label for="new_pass" class="sr-only">Mot de passe</label>
                        <div class="div-user-pass">
                            <input type="password" name="new_pass" id="new_pass" class="form-control mb-2 shadow-sm" placeholder="Nouveau mot de passe">
                            <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                        </div>   
                        <div class="div-user-pass">
                            <input type="password" name="new_pass_confirm" id="new_pass_confirm" class="form-control mb-4" placeholder="Confirmation du mot de passe">
                            <div id="showConfirmPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                        </div>
                    <input type="submit" value="Envoyer" id="submit" class="btn btn-lg btn-info btn-block mb-4 shadow">

                    <?php include("msg_session_flash.php") ?>
                </form>
            </div>
        </section>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>
    <script src="js/show_password.js"></script>
    <script src="js/show_confirm_password.js"></script>

</body>

</html>