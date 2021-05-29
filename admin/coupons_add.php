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
include 'models/CouponModels.php';

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
$couponModel = new CouponModel($db);

$cpid = $_POST['cpid'];
$cpdesc = $_POST['cpdesc'];
$minord = $_POST['minord'];
$maxdisc = $_POST['maxdisc'];
$discount = $_POST['discount'];
$maxuse = $_POST['maxuse'];

$couponModel->addCoupon(
    $cpid,
    $cpdesc,
    $minord,
    $maxdisc,
    $discount,
    $maxuse
);

header('Location: coupons_index.php');
die();
