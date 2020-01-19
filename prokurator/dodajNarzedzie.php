<?php
    session_start();

    $flag = true;

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $sprawa_id = test_input($_POST['sprawa_id']);
        $narzedzieN = test_input($_POST['narzedzieN']);
        $numer_dowoduN = test_input($_POST['numer_dowoduN']);
        $miejsce_przechowaniaN = test_input($_POST['miejsce_przechowaniaN']);

        $_SESSION['sprawa_id'] = $_POST['sprawa_id'];
        $_SESSION['narzedzieN'] = $_POST['narzedzieN'];
        $_SESSION['numer_dowoduN'] = $_POST['numer_dowoduN'];
        $_SESSION['miejsce_przechowaniaN'] = $_POST['miejsce_przechowaniaN'];
    }
    
    $numer_dowoduN = preg_replace("/[^0-9]+/", "", $numer_dowoduN);
    $narzedzieN = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ]+/", "", $narzedzieN);
    $miejsce_przechowaniaN = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ]+/", "", $miejsce_przechowaniaN);


    if($narzedzieN != $_POST['narzedzieN'] || strlen($narzedzieN) < 2){
        $_SESSION['narzedzieNERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }
    if($numer_dowoduN != $_POST['numer_dowoduN'] || strlen($numer_dowoduN) != 5){
        $_SESSION['numer_dowoduNERROR'] = "Numer dowodu składa się z 5 cyfr!";
        $flag = false;
    }
    if($miejsce_przechowaniaN != $_POST['miejsce_przechowaniaN'] || strlen($miejsce_przechowaniaN) < 2){
        $_SESSION['miejsce_przechowaniaNERROR'] =  "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }


    if($flag) {
        $dbname      = "dbname = u7fil";
        $credentials = "user = u7fil password=7fil";
     
        $db = pg_connect( "$dbname $credentials");
        if(!$db) {
            $_SESSION['error'] ="Brak połączenia z bazą danych\n";
            $flag = false;
        }

        //Dodaje narzedzieN
        //sprawdzam czy numer dowodu sie nie duplikuje
        $quest = "SELECT * FROM kartoteka.narzedzie n WHERE n.numer_dowodu = $numer_dowoduN";
        $val = pg_query($db, $quest);
        if(!$val) {
            $_SESSION['error'] ="Błąd serwera! ".$quest;
            $flag = false;
        } 
        else {
            $tab = pg_fetch_all($val);
        }

        if($tab[0]['id']){ //jeżeli istnieje taki numer
            $_SESSION['numer_dowoduNERROR'] ="Podany numer dowodu już istnieje!";
            $flag = false;
        }
        else{
            $quest = "INSERT INTO kartoteka.narzedzie (narzedzie, numer_dowodu, miejsce_przechowania, przestepstwo_id) 
                        VALUES  ('$narzedzieN', $numer_dowoduN, '$miejsce_przechowaniaN', $sprawa_id);";
            $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error']= " Błąd serwera! ".$quest;
                $flag = false;
            } 
        }
        if($flag) {
            //session_destroy();
            $_SESSION['error'] = "Dodano przestępstwo!";
            $_SESSION['narzedzieN'] = "";
            $_SESSION['numer_dowoduN'] = "";
            $_SESSION['miejsce_przechowaniaN'] = "";
            header ('Location: start.php');   
        }
        else {
            $_SESSION['error'] = "Błąd gdzies w bazie danych!";
            header ('Location: start.php'); 
        }

        pg_close($db);
    }
    else {
        header ('Location: start.php'); 
    }
    
?>
