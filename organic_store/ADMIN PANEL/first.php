<?php

#To be removed during deployment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);
$dbname = 'kare2';
$host = 'localhost';
$port = '3306';
$charset = 'utf8';
$username = 'root';
$password = '';


$pdo = new PDO('mysql:dbname=' . $dbname . ';host=' . $host . ';port=' . $port . ';charset=' . $charset, $username, $password);
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

#Enter your sql query here
$sql = 'SELECT * FROM customer;';   
$prep = $pdo->prepare($sql);
// $prep->execute(['id' => 'a001']); // associative array
$prep->execute([]);
$result = $prep->fetchAll();

// foreach($result as $row){
//     echo $row[1]."<br>";
// }

echo $twig->render('trial.twig', [
    'name' => 'John Doe',
    'occupation' => 'gardener',
    'custs' => $result
]);
