<?php

#To be removed during deployment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
include 'models/ProductModels.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

#Setting up Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
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

$pdname = $_POST['pdname'];
$ing = $_POST['ingredients'];
$ben = $_POST['benefits'];
$app = $_POST['application'];
$tags = $_POST['tags'];
$p30 = $_POST['30ml'];
$p50 = $_POST['50ml'];
$p100 = $_POST['100ml'];
$p250 = $_POST['250ml'];
$cat = $_POST['categories'];
$conc = $_POST['concerns'];
$media = $_FILES['media'];

$productModel->addProduct(
    $pdname,
    $ing,
    $ben,
    $app,
    $tags,
    $p30,
    $p50,
    $p100,
    $p250,
    $cat,
    $conc,
    $media
);

header('Location: product_index.php');
die();
// print_r($media);
// foreach ($_POST as $val) {/
//     if (gettype($val) == 'array') {
//         foreach ($val as $v) {
//             echo $v . " " . gettype($v) . "<br>";
//         }
//         echo "<br>";
//     } else {
//         echo $val . " " . gettype($val) . "<br><br>";
//     }
// }

// print_r($_FILES);
// foreach ($_FILES as $val) {
//     if (gettype($val) == 'array') {
//         foreach ($val as $v) {
//             echo $v . " " . gettype($v) . "<br>";
//         }
//         echo "<br>";
//     } else {
//         echo $val . " " . gettype($val) . "<br><br>";
//     }
// }