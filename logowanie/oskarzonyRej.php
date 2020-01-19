<?php
session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  	$_SESSION['imieR'] = $_POST['imieR'];
  	$_SESSION['nazwiskoR'] = $_POST['nazwiskoR'];
  	$_SESSION['emailR'] = $_POST['emailR'];
  	$_SESSION['passR'] = $_POST['passR'];
    }

    $imieR = $_SESSION['imieR'];
    $nazwiskoR = $_SESSION['nazwiskoR'];
    $emailR = $_SESSION['emailR'];
    $hasloR = $_SESSION['passR'];

    if (empty($_POST["imieR"])) {
        $_SESSION['imieR_error'] = "Podaj imię!";
        header('Location: oskarzonyLog.php');
        
    }
    if(empty($_POST["nazwiskoR"])){
        $_SESSION['nazwiskoR_error'] = "Podaj naziwsko!";
        header('Location: oskarzonyLog.php');
        
    }
    if(empty($_POST["passR"])){
        $_SESSION['hasloR_error'] = "Podaj hasło!";
        header('Location: oskarzonyLog.php');
        
    }
    if (empty($_POST["emailR"])) {
        $_SESSION['emailR_error'] = "Podaj e-mail!";
        header('Location: oskarzonyLog.php');
        
    }

    else {
        $dbname      = "dbname = u7fil";
   	    $credentials = "user = u7fil password = 7fil";

        $db = pg_connect( "$dbname $credentials");
        if(!$db) {
            $_SESSION['emailR_error'] = "Brak ".$query."  połączenia z bazą danych!";
            header('Location: oskarzonyLog.php');
                
        }

        $query = "SELECT * FROM kartoteka.oskarzony WHERE imie = '$imieR' and nazwisko = '$nazwiskoR';";
        $result = pg_query($db, $query);
        
        if(!$result) {
            $_SESSION['emailR_error'] = "Brak ".$query."  dostępu do bazy danych!";
            header('Location: oskarzonyLog.php');
        
        }
        else {
            $tab = pg_fetch_all($result);
            if($tab[0]['id'] === null) {
            $_SESSION['imieR_error'] = "Oskarżony o takim imieniu i nazwisko nie istnieje w bazie! Musisz poczekać, aż wpiszą Cię do akt!";
                header('Location: oskarzonyLog.php');
            }
            if($tab[0]['email'] !== null){
                $_SESSION['imieR_error'] = "Przecież już masz konto, twoje hasło to ".$tab[0]['haslo']."!";
                header('Location: oskarzonyLog.php');
            }
            else {
                $query = "  UPDATE  kartoteka.oskarzony 
                            SET email = '$emailR',
                                haslo = '$hasloR'
                            WHERE imie = '$imieR' AND nazwisko = '$nazwiskoR';";
                $result = pg_query($db, $query);
                
                if(!$result) {
                    $_SESSION['emailR_error'] = "Brak ".$query."  dostępu do bazy danych!";
                    header('Location: oskarzonyLog.php');
                
                }

            }        

            $_SESSION['passR'] = '';
            $_SESSION['imieR'] = '';
            $_SESSION['nazwiskoR'] = '';
            $_SESSION['emailR'] = '';
            $_SESSION['emailR'] = '';

            header("Location: oskarzonyLog.php");
            }

        }
    pg_close($db); 
?>


