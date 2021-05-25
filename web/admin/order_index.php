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

require __DIR__ . '/vendor/autoload.php';
include 'models/OrderModels.php';

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
$orderModel = new OrderModel($db);

$orders = $orderModel->getAllOrders();
echo $twig->render('order_index.twig', [
    'user' => $_SESSION,
    'page_title' => 'Order List',
    'section' => 'Order',
    'subsection' => 'Order List',
    'orders' => $orders
]);
