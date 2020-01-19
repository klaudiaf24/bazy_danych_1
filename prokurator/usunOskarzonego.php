<?php
    session_start();
    $dbname      = "dbname = u7fil";
    $credentials = "user = u7fil password=7fil";
 
    $db = pg_connect( "$dbname $credentials");
    if(!$db) {
        $_SESSION['error'] ="Brak połączenia z bazą danych\n";
        $flag = false;
    }
    $id = $_POST['oskarzony_id'];

    $sql2 = "SELECT * FROM kartoteka.usun_oskarzonego($id);";
    $wynik2 = pg_query($db, $sql2);
    if(!$wynik2) {
        $_SESSION['error'] = "Błąd w bazie danych".$sql2;
        header('Location: start.php');
        exit();
    }
    else {
        $_SESSION['error'] = "Uniewinniono!";
        header('Location: start.php');
        exit();
    }
     
?>
