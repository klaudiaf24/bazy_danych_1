<?php
session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  	$_SESSION['email'] = $_POST['email'];
  	$_SESSION['pass'] = $_POST['pass'];
    }

    $email = $_SESSION['email'];
    $haslo = $_SESSION['pass'];

    if (empty($_POST["email"])) {
        $_SESSION['email_error'] = "Podaj e-mail!";
        header('Location: prokuratorLog.php');
        exit();
    }
    else if(empty($_POST["pass"])){
        $_SESSION['haslo_error'] = "Podaj hasło!";
        header('Location: prokuratorLog.php');
        exit();
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
	 $_SESSION['email_error'] = "Zły fromat e-mail!";
         header('Location: prokuratorLog.php');
        exit();
    }
    else {
        $dbname      = "dbname = u7fil";
   	$credentials = "user = u7fil password = 7fil";

   	$db = pg_connect( "$dbname $credentials");
   	if(!$db) {
   	       $_SESSION['email_error'] = "Brak połączenia z bazą danych!";
	       header('Location: prokuratorLog.php');
               exit();
	}

        $query = "SELECT * FROM kartoteka.prokurator WHERE email = '$email' and haslo = '$haslo';";
        $result = pg_query($db, $query);
	
	if(!$result) {
		$_SESSION['email_error'] = "Brak dostępu do bazy danych!";
		header('Location: prokuratorLog.php');
		exit();
	
	}
        else {
            $tab = pg_fetch_all($result);
	    if($tab[0]['id'] === null) {
	       $_SESSION['email_error'] = "Prokurator o tym e-mail nie istnieje!";
               header('Location: prokuratorLog.php');
	       exit();
	    }
            
            $_SESSION['id'] = $tab[0]['id'];
            $_SESSION['imie'] = $tab[0]['imie'];
            $_SESSION['nazwisko'] = $tab[0]['nazwisko'];
            $_SESSION['email'] = $tab[0]['email'];
	    $_SESSION['miejsce_pracy'] = $tab[0]['miejsce_pracy'];
	    header("Location: ../prokurator/start.php");
        }

    }
    pg_close($db); 
?>

