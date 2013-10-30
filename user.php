<?php

class User {

	private $hashedPsw = "";


	public function login($inputName, $inputPsw) {

		$username = "Admin";
		$password = "Password";
		$helpText = "";

		//vid en lyckad inloggning	
		if ($inputName == $username && $inputPsw == $password) {		
			
			//starta en session 
			$_SESSION["login"] = 1;		

			//$this->verifiedUser($inputName);

			//ladda om sidan så scriptet körs igen
			header("Location: $_SERVER[PHP_SELF]");			
		}

		//felhantering av inmatad data från användaren
		else if (isset($_POST["submit"])){		
			if(empty($inputName) ) {
				return $helpText= "Användarnamn saknas";
			}

			else if(empty($inputPsw) ) {
				return $helpText= "Lösenord saknas";
			}

			else {
				return $helpText= "Felaktigt användarnamn och/eller lösenord";
			}
		}
	}

	public function verifiedUser($inputName) {

		if (isset($_SESSION["login"])) {

			if ($this->testSession()==true) {
						
				echo "<h2> $inputName är inloggad</h2>";			
				
				$_SESSION["login"] = $_SESSION["login"]+1;

				if ($_SESSION["login"] < 5) {
					
					if (isset($_COOKIE["username"])) {
						?>
						<p>Inloggningen lyckades och vi kommer ihåg dig nästa gång</br></p>
						<?php
					}

					else {
						?>
						<p>Inloggningen lyckades </br></p>
						<?php
					}
				}
			
				?>
				<a href="?logout">Logga ut</a>
				<?php
			}

			else {
				//$this->logout();
				unset($_SESSION["login"]);
				session_destroy();
				header("Location: $_SERVER[PHP_SELF]");


			}
			
		}
	}

	public function StoreUser ($inputName, $inputPsw) {

		$this->login($inputName, $inputPsw);

		if (isset($_SESSION["login"])) {			
			$this->hashedPsw = crypt($inputPsw); 
			$endtime = strtotime( '+5 min' );

			file_put_contents("endtime.txt", "$endtime");
			file_put_contents("password.txt", "$this->hashedPsw");

			setcookie("username", $inputName, $endtime);
			setcookie("password", $this->hashedPsw, $endtime);
		}		
	}

	public function cookieLogin (){

		//$hashedPsw;
		$inputName = $_COOKIE["username"];
		$inputPsw = $_COOKIE["password"];
		
		$correctPsw = file_get_contents("password.txt");		

		if ($inputPsw == $correctPsw) {
						
			if ($this->testSession()==true && $this->testCookie()==true) {
				//logga in
				//$this->StoreUser($inputName, $inputPsw);

				//$inputName = $_COOKIE["username"];

				$_SESSION["login"] = 1;

				echo "<h2> $inputName är inloggad</h2>";

				?>
				<p>Inloggningen lyckades via cookies</br></p>
				<?php		

				?>
				<a href="?logout">Logga ut</a>
				<?php
			}
		}		
		
		else {
			echo "<p>Felaktig information i cookie</p><br/>";

				setcookie("username", "ended", strtotime( '-1 min' ));
				setcookie("password", "ended", strtotime( '-1 min' ));

				unset($_SESSION["login"]);
				session_destroy();

				//ladda om sidan så scriptet körs igen
				//header("Location: $_SERVER[PHP_SELF]");
		}
	}

	public function testSession() {

		$sessionLocation = "testSession";

		if(isset($_SESSION[$sessionLocation]) == false) {
			$_SESSION[$sessionLocation] = array();
			$_SESSION[$sessionLocation]["browser"] = $_SERVER["HTTP_USER_AGENT"];
			$_SESSION[$sessionLocation]["ip"] = $_SERVER["REMOTE_ADDR"];
		}

		if ($_SESSION[$sessionLocation]["browser"] != $_SERVER["HTTP_USER_AGENT"] ||
			$_SESSION[$sessionLocation]["ip"] != $_SERVER["REMOTE_ADDR"]) {
			
			//echo "<p>Felaktig information i cookie</p><br/>";	
			return false;
		}

		else {
			return true;
		}

	}

	public function testCookie() {

		if (isset($_COOKIE["username"])) {
			$cookieEndTime = file_get_contents("endtime.txt");
			//echo "$cookieEndTime";

			if (time() > $cookieEndTime) {
				//tillbaka t formuläret
				return false;
			} 

			else {
				//logga in
				return true;
			}
		}
	}
	
	public function logout () {	
		
		if(!isset($_SESSION["login"])) {
				$helpText="";
		}

		else {	

			echo "<p>Du har nu loggat ut</p><br/>";

			setcookie("username", "ended", strtotime( '-1 min' ));
			setcookie("password", "ended", strtotime( '-1 min' ));

			unset($_SESSION["login"]);
			session_destroy();

			header("Location: $_SERVER[PHP_SELF]");

			//$view = new View();
			//$view->displayForm("<p>Du har nu loggat ut</p><br/>");

			//header("Location: ?logout");

			//echo "<p>Du har nu loggat ut</p><br/>";
		}	
	}
}	//class