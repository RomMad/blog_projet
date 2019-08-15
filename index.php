<?php
spl_autoload_register("loadClass", "model/");

$session = new Session();
$session->connect();

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
            }
            break;
        case "editPost":
            postEdit();
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
            user();
            break;             
        default:
            listPosts();
    } 
} else {
    listPosts();
}

function loadClass($classname) {
    require "model/" . $classname . ".php";
}

// $controllers = [
//     "listPosts" => "frontend",
//     "post",
//     "profil",
//     "inscription",
//     "connection",
//     "forgotPassword",
//     "resetPassword",
//     "postEdit",
//     "comments",
//     "posts",
//     "settings",
//     "users",
//     "newUser"
// ];

// var_dump($controllers);

// if (isset($_GET["action"])) {
//     foreach($controllers as $controller) {
//         if ($_GET["action"] == $controller) {
//             $controller();
//         }
//     }
// } else {
//     listPosts();
// }