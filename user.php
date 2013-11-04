<?php

class User {
	//sträng med krypterat lösenord
	private $hashedPsw = "";

	//testa input från användaren, logga in om korrekt
	public function login($inputName, $inputPsw) {

		$username = "Admin";
		$password = "Password";
		$helpText = "";

		//vid en lyckad inloggning	
		if ($inputName == $username && $inputPsw == $password) {				
			 
			$_SESSION["login"] = 1;			
			
			header("Location: $_SERVER[PHP_SELF]");			
		}

		//felhantering av inmatad data från användaren, returnerar sträng m hjälptext
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

	//visa feedback på inloggning
	public function verifiedUser($inputName) {

		if (isset($_SESSION["login"])) {
			if ($this->testSession()==true) {						
				echo "<h2> $inputName är inloggad</h2>";				
				$_SESSION["login"] = $_SESSION["login"]+1;

				if ($_SESSION["login"] < 4) {					
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
				unset($_SESSION["login"]);
				session_destroy();
				header("Location: $_SERVER[PHP_SELF]");
			}			
		}
	}

	//skapa cookies
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

	//logga in utifrån cookies
	public function cookieLogin (){

		
		$inputPsw = $_COOKIE["password"];
		
		$correctPsw = file_get_contents("password.txt");
		$results = false;		

		if ($inputPsw == $correctPsw) {

			if ($this->testSession()==true && $this->testCookie()==true) {
				$results = true;
			}

			$this->cookieLoginResults($results);
		}
						
		else {

			$this->cookieLoginResults($results);
		}

	}

	//visa resultat av login med cookie
	private function cookieLoginResults($results) {

		if($results == true) {
			$_SESSION["login"] = 1;

			$inputName = $_COOKIE["username"];

			echo "<h2> $inputName är inloggad</h2>";

			?>
			<p>Inloggningen lyckades via cookies</br></p>
			<?php		

			?>
			<a href="?logout">Logga ut</a>
			<?php
		}

		else {
			echo "<p>Felaktig information i cookie</p><br/>";

			setcookie("username", "ended", strtotime( '-1 min' ));
			setcookie("password", "ended", strtotime( '-1 min' ));

			unset($_SESSION["login"]);
			unset($_SESSION["testSession"]);
			session_destroy();
		}

	}

	//testar sessionen mot kapning
	//true loggar in, false tillbaka till formuläret
	public function testSession() {

		$sessionLocation = "testSession";

		if(isset($_SESSION[$sessionLocation]) == false) {
			$_SESSION[$sessionLocation] = array();
			$_SESSION[$sessionLocation]["browser"] = $_SERVER["HTTP_USER_AGENT"];
			$_SESSION[$sessionLocation]["ip"] = $_SERVER["REMOTE_ADDR"];
		}

		if ($_SESSION[$sessionLocation]["browser"] != $_SERVER["HTTP_USER_AGENT"] ||
			$_SESSION[$sessionLocation]["ip"] != $_SERVER["REMOTE_ADDR"]) {
			
			return false;
		}

		else {
			return true;
		}

	}

	//testar att cookien är aktuell
	//true loggar in, false tillbaka till formuläret
	public function testCookie() {

		if (isset($_COOKIE["username"])) {
			$cookieEndTime = file_get_contents("endtime.txt");

			if (time() > $cookieEndTime) {
				return false;
			} 

			else {
				return true;
			}
		}
	}
	
	//logga ut & förstör cookies och sessionen
	public function logout () {	
		
		if(!isset($_SESSION["login"])) {
				return "";
		}

		else {				

			setcookie("username", "ended", strtotime( '-1 min' ));
			setcookie("password", "ended", strtotime( '-1 min' ));

			unset($_SESSION["login"]);
			session_destroy();

			//header("Location: $_SERVER[PHP_SELF]");

			return "<p>Du har nu loggat ut</p><br/>";

			//$view = new View();
			//$view->displayForm("<p>Du har nu loggat ut</p><br/>");

			//header("Location: ?logout");

			//echo "<p>Du har nu loggat ut</p><br/>";
		}
	}
}	//class