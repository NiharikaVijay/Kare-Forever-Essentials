<?php

#To be removed during deployment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
include dirname(dirname(__FILE__)) . '/models/admin/ProductModels.php';

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

#Main code, changes for every view
$productModel = new ProductModel($db);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo $twig->render('ingredients_add.twig', [
        'user' => $_SESSION,
        'page_title' => 'Add Ingredient',
        'section' => 'Ingredients',
        'subsection' => 'Add Ingredient',
    ]);
} else {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    $productModel->addIngredient($name, $description, $image);

    header('Location: ingredients_index.php');
    die();
}
