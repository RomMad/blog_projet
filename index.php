<?php
spl_autoload_register();

$session = new model\Session();
$session->connect();

if(!isset($_SESSION["settings"])) {
    $settingsManager = new model\SettingsManager();
    $_SESSION["settings"] = $settingsManager->get();
}

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "listPosts":
            $listsPosts = new controller\frontend\ListPostsController($session);
            break;
        case "post":
            if (isset($_GET["id"]) && $_GET["id"] > 0) {
                $listsPosts = new controller\frontend\PostController($session);
            } else {
                error404();
            }
            break;
        case "editPost":
            if (!isset($_GET["id"]) || (isset($_GET["id"]) && $_GET["id"] > 0)) {
                $postEdit = new controller\backend\PostEditController($session);
            } else {
                error404();
            }
            break;
        case "profil":
            $profilController = new controller\frontend\ProfilController($session);
            break;
        case "inscription":
            $inscriptionController = new controller\frontend\InscriptionController($session);
            break;
        case "connection":
            $connectionController = new controller\frontend\ConnectionController($session);
            break;
        case "disconnection":
            $session->disconnect();
            break;
        case "forgotPassword":
            $forgotPasswordController = new controller\frontend\ForgotPasswordController($session);
            break;
        case "resetPassword":
            $resetPasswordController = new controller\frontend\ResetPasswordController($session);
            break;  
        case "comments":
            $listCommentsController = new controller\backend\ListCommentsController($session);
            break;
        case "posts":
            $listPostsController = new controller\backend\ListPostsController($session);
            break;
        case "users":
            $listUsersController = new controller\backend\ListUsersController($session);
            break;
        case "newUser":
            $newUserController = new controller\backend\NewUserController($session);
            break;
        case "settings":
            $SettingsController = new controller\backend\SettingsController($session);
            break;
        case "user":
            if (isset($_GET["id"]) && $_GET["id"] > 0) {
                $userController = new controller\backend\UserController($session);
            } else {
                error404();
            }
            break;   
        case "error403":
            error403();
            break;      
        case "error404":
            error404();
            break;                  
        default:
            error404();
    } 
} else {
    $listsPostsController = new controller\frontend\ListPostsController($session);
}

function error403() {
    require "error/403.php";
}

function error404() {
    require "error/404.php";
}
