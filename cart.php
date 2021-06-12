<?php


session_start();

if (!isset($_SESSION['cxid'])) {
    include __DIR__ . '/models/user/HelperModels.php';

    $helperModel = new HelperModel($db);
    $_SESSION['cxloggedin'] = false;
    $_SESSION['cxid'] = $helperModel->generateRandomString(5);
}

# TODO To be removed during deployment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
#Include whatever model you want here
include __DIR__ . '/models/user/CustomerModels.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

#Setting up Twig
$loader = new FilesystemLoader(__DIR__ . '/templates/user');
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
$customerModel = new CustomerModel($db);

$cxid = $_SESSION['cxid'];
if (isset($_GET['coupon'])) {
    $cart = $customerModel->getCart($cxid, $_GET['coupon']);
    // print_r($cart);
} else {
    $cart = $customerModel->getCart($cxid);
}
$params = [
    'account' => $_SESSION,
    'cart' => $cart['items'],
    'subtotal' => $cart['subtotal'],
    'delivery' => $cart['delivery'],
    'total' => $cart['subtotal'] + $cart['delivery']
];
if (isset($cart['discount'])) {
    $params += array('discount' => $cart['discount'], 'coupon' => $_GET['coupon']);
}
echo $twig->render('cart.twig', $params);
// print_r(sizeof($params));
// print_r($_GET);