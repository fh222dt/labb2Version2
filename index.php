<!DOCTYPE html>
<?php 
session_start();
require_once("user.php");
require_once("view.php");

?>

<html>
	<head>
	<meta charset="utf-8">
	<title>Laboration 2</title>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
	<link rel="stylesheet" href="bootstrap/css/bootstrap-responsive.css">
	</head>

	<body>
 
	<h1>Laborationskod fh222dt</h1>
	
	<?php

	$html = new View();
	$userObject = new User();
	$inputName ="";
	$inputPsw ="";

if(!isset($_SESSION["login"]) && !isset($_COOKIE["username"])) {
	echo "<h2>Ej inloggad</h2>";
}


if (isset($_GET["logout"]) && isset($_SESSION["login"])) {
	$stopLogin = $userObject->logout();
	echo $html->displayForm($stopLogin);
}

if (isset($_POST["submit"]) && !isset($_POST["KeepLogin"])) {
	$inputName = $_POST["UserName"];
	$inputPsw = $_POST["Password"];

	$userObject->login($inputName, $inputPsw);
}

if (isset($_POST["submit"]) && isset($_POST["KeepLogin"])) {

	$inputName = $_POST["UserName"];
	$inputPsw = $_POST["Password"];

	$userObject->StoreUser($inputName, $inputPsw);
}


if (isset($_SESSION["login"])) {

	$userObject->verifiedUser("Admin");
}

if (isset($_COOKIE["username"]) && isset($_SESSION["login"])== null ) {
	$userObject->cookieLogin();
}

if (empty($_COOKIE) || empty($_SESSION)) {
		echo $html->displayForm($userObject->login($inputName, $inputPsw));
}
	
echo $html->displayDate();

?>

	</body>
</html>