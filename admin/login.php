<?php

#To be removed during deployment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
include dirname(dirname(__FILE__)) . '/models/admin/AdminModels.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

#Setting up Twig
$loader = new FilesystemLoader(dirname(dirname(__FILE__)) . '/templates/admin');
$twig = new Environment($loader);

#Setting up PDO
$dbname = 'kare2';
$host = 'localhost';
$port = '3306';
$charset = 'utf8';
$username = 'root';
$password = '';
$db = new PDO('mysql:dbname=' . $dbname . ';host=' . $host . ';port=' . $port . ';charset=' . $charset, $username, $password);
$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo $twig->render('login.twig');
} else {
    $adminModel = new AdminModel($db);

    $email = $_POST['email'];
    $pwd = $_POST['pwd'];

    $admin = $adminModel->verify($email, $pwd);

    // print_r($_POST);
    if ($admin['verified']) {
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['admid'] = $admin['details'][0][0];
        $_SESSION['name'] = $admin['details'][0][1];
        $_SESSION['email'] = $admin['details'][0][2];
        $_SESSION['phone'] = $admin['details'][0][3];
        $_SESSION['avatar'] = $admin['details'][0][5];

        header("Location: /admin");
        die();
    }

    header("Location: /admin");
    die();
}