<?php
    session_start();
    $dbname      = "dbname = u7fil";
    $credentials = "user = u7fil password=7fil";
 
    $db = pg_connect( "$dbname $credentials");
    if(!$db) {
        $_SESSION['error'] ="Brak połączenia z bazą danych\n";
        $flag = false;
    }
    $id = $_POST['przestepstwo_id_usun'];

    $sql2 = "DELETE FROM kartoteka.narzedzie WHERE id = $id;";
    $wynik2 = pg_query($db, $sql2);
    if(!$wynik2) {
        $_SESSION['error'] = "Błąd w bazie danych";
        header('Location: start.php');
        exit();
    }
    else {
        $_SESSION['error'] = "Pozbyto się dowodu!";
        header('Location: start.php');
        exit();
    }
     
?>
