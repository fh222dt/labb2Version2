<!DOCTYPE html>
<?php 
session_start();	//starta sessionen
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
//variabler
$formHeader ="<h2>Ej Inloggad</h2>";
$helpText ="";
$displayForm =true;
//endast dessa variabler går att logga in med
$username = "Admin";
$password = "Password";

//echo $formHeader;


//logga ut
if (isset($_GET["logout"])) {		//kolla om det finns i urlen	
	
	if(!isset($_SESSION["login"])) {
			$helpText="";
	}

	else {
		$helpText= "<p>Du har nu loggat ut</p><br/>";
	unset($_SESSION["login"]);
	session_destroy();
	}
}
	
//testa om formuläret är skickat
if (isset($_POST["submit"])) {

	$inputName = $_POST["UserName"];
	$inputPsw = $_POST["Password"];	
	
	//vid en lyckad inloggning	
	if ($inputName == $username && $inputPsw == $password) {		
		//starta en session 
		$_SESSION["login"] = 1;

		//ladda om sidan så scriptet körs igen
		header("Location: $_SERVER[PHP_SELF]");
	}

	//felhantering av inmatad data från användaren
	else {		
		if(empty($inputName) ) {
		$helpText= "<p>Användarnamn saknas</p><br/>";
		}

		else if(empty($inputPsw) ) {
			$helpText= "<p>Lösenord saknas</p><br/>";
		}

		else {
			$helpText= "<p>Felaktigt användarnamn och/eller lösenord</p><br/>";
		}
	}
}

//när man har loggat in visas detta
if (isset($_SESSION["login"])) {

	?>
	<h2> Admin är inloggad</h2>		
	<?php 							//ändra namn till en variabel!!!!
	
	$_SESSION["login"] = $_SESSION["login"]+1;

	if ($_SESSION["login"] == 3) {				//varför är den 3?
		?>
		<p>Inloggningen lyckades </br> <a href="?logout=true">Logga ut</a></p>
		<?php
	}

	else {
		?>
		<a href="?logout=true">Logga ut</a>
		<?php
	}

	$displayForm = false;

}



//visa formuläret
if ($displayForm == true) {	
	
	echo $formHeader;
	?>
	<form method="post" action="index.php" class="form-inline">
		<fieldset>
			<legend>Login - Skriv in användarnamn och lösenord</legend>

			<?php echo $helpText; ?>

			<label for="UserName">Användarnamn</label>
			<input id="UserName" name="UserName" type="text" size="15" 
			value="<?php echo isset($_POST['UserName']) ? $_POST['UserName'] : '' ?>">		<!--value anv för att behålla inmatad text -->

			<label for="Password">Lösenord</label>
			<input id="Password" name="Password" type="password" size="15">

			<label for="KeepLogin" class="checkbox"> 
			<input id="KeepLogin" type="checkbox"> Håll mig inloggad</label>

			<input type="submit" name="submit" value="Logga in" class="btn">
		</fieldset>
	</form> <?php
}

//Visa datum och tid snyggt på svenska
setlocale (LC_TIME, "Swedish");
echo utf8_encode(strftime("<p>%A")); 
echo strftime(", den " . "%#d %B %Y" . ". Klockan är [" . "%X" . "]</p>"); //formatet %#d är linux %e	anv %T ist f %X på servern
?>
</body>
</html>