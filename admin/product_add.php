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

$productModel = new ProductModel($db);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $details = $productModel->getCatConIngs();
    echo $twig->render('product_add.twig', [
        'user' => $_SESSION,
        'page_title' => 'Add Product',
        'section' => 'Product',
        'subsection' => 'Add Product',
        'categories' => $details['cat'],
        'concerns' => $details['conc'],
        'ings' => $details['ings']
    ]);
} else {
    $pdname = $_POST['pdname'];
    $desc = $_POST['description'];
    $ben = $_POST['benefits'];
    $app = $_POST['application'];
    $tags = $_POST['tags'];
    $p30 = $_POST['30ml'];
    $p50 = $_POST['50ml'];
    $p100 = $_POST['100ml'];
    $p250 = $_POST['250ml'];
    $cat = $_POST['categories'];
    $conc = $_POST['concerns'];
    $ings = $_POST['ings'];
    $discount = $_POST['discount'];
    $isfeatured = $_POST['isfeatured'];
    $media = $_FILES['media'];

    if ($p30 == 0) {
        $p30 = null;
    }
    if ($p50 == 0) {
        $p50 = null;
    }
    if ($p100 == 0) {
        $p100 = null;
    }
    if ($p250 == 0) {
        $p250 = null;
    }

    $productModel->addProduct(
        $pdname,
        $desc,
        $ings,
        $ben,
        $app,
        $tags,
        $p30,
        $p50,
        $p100,
        $p250,
        $cat,
        $conc,
        $discount,
        $isfeatured,
        $media,
    );

    header('Location: product_index.php');
    die();
}
