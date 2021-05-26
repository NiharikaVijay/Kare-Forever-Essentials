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

require dirname(dirname(__FILE__)). '/vendor/autoload.php';
include 'models/CustomerModels.php';

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
$customerModel = new CustomerModel($db);

$cxid = $_GET['cxid'];
$details = $customerModel->getCustomerDetails($cxid);
$orders = $customerModel->getCustomerOrders($cxid);
// echo var_dump($details);
echo $twig->render('customer_details.twig', [
    'user' => $_SESSION,
    'page_title' => 'Customer Details',
    'section' => 'Customer',
    'subsection' => 'Details',
    'details' => $details,
    'orders' => $orders
]);
