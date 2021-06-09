<?php
session_start();
session_destroy();
// TODO Redirect to the login page:
header('Location: skinkare.php');
die();
