<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pdo = new PDO('mysql:dbname=kare2;host=localhost;port=3306;charset=utf8','root','');

$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

$sql = 'select * from customer where cxid= :id;';
$prep = $pdo->prepare($sql);
$prep->execute(['id' => 'a001']); // associative array
// $prep->execute();
$result = $prep->fetchAll();

// $query = "SELECT * from customer where id= :id";
// $prep = $pdo->prepare($query);
// $prep = $p
// $prep->execute(['id' => 'a001']);
// echo "executed";
// $customers = $prep->fetchAll();
// echo $customers;
foreach($result as $row){
    echo $row[1]."<br>";
}
// echo "fetched";
// $result = $prep->setFetchMode(PDO::FETCH_ASSOC);
//   foreach($stmt->fetchAll()) as $k=>$v) {
//     echo $v;
//   }
?>
