<?php
session_start();

if (!isset($_SESSION['cxid'])) {
    include __DIR__ . '/models/user/HelperModels.php';

    $helperModel = new HelperModel($db);
    $_SESSION['cxloggedin'] = false;
    $_SESSION['cxid'] = $helperModel->generateRandomString(5);
}

#TODO to be removed during deployment
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
$otp = $_POST['first'] . $_POST['second'] . $_POST['third'] . $_POST['fourth'];

$result = $customerModel->verifyOTP($cxid, $otp);

if ($result['isValid']) {
    $_SESSION['cxid'] = $result['details'][0];
    $_SESSION['fname'] = explode(" ",$result['details'][1])[0];
    $_SESSION['cxloggedin'] = true;
    // TODO change to index.php when done
    header("Location: skinkare.php");
    die();
} else {
    $_SESSION['error'] = 'Invalid OTP';
    header("Location: login.php");
    die();
}
