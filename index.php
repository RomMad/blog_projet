<?php
spl_autoload_register();

$session = new model\Session();
$session->connect();

$settingsManager = new model\SettingsManager();
if(!isset($_SESSION["blog_name"])) {
    $settings = $settingsManager->get();
    $_SESSION["blog_name"] = $settings->blog_name();
}

require "controller/frontend/listPosts.php";
require "controller/frontend/post.php";
require "controller/frontend/profil.php";
require "controller/frontend/inscription.php";
require "controller/frontend/connection.php";
require "controller/frontend/forgotPassword.php";
require "controller/frontend/resetPassword.php";
require "controller/backend/postEdit.php";
require "controller/backend/comments.php";
require "controller/backend/posts.php";
require "controller/backend/settings.php";
require "controller/backend/users.php";
require "controller/backend/newUser.php";
require "controller/backend/user.php";

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {
        case "listPosts":
            listPosts();
            break;
        case "post":
            if (isset($_GET["id"]) && $_GET["id"] > 0) {
                post();
            } else {
                error404();
            }
            break;
        case "editPost":
            if (!isset($_GET["id"]) || (isset($_GET["id"]) && $_GET["id"] > 0)) {
                postEdit();
            } else {
                error404();
            }
            break;
        case "profil":
            profil();
            break;

        case "inscription":
            inscription();
            break;
        case "connection":
            connection();
            break;
        case "disconnection":
            $session->disconnect();
            break;
        case "forgotPassword":
            forgotPassword();
            break;
        case "resetPassword":
            resetPassword();
            break;  
        case "comments":
            comments();
            break;
        case "posts":
            posts();
            break;
        case "settings":
            settings();
            break;
        case "users":
            users();
            break;
        case "newUser":
            newUser();
            break;
        case "user":
            if (isset($_GET["id"]) && $_GET["id"] > 0) {
                user();
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
    listPosts();
}

function error403() {
    require "error/403.php";
}

function error404() {
    require "error/404.php";
}

// function loadClass($classname) {
//     require "model/" . $classname . ".php";
// }