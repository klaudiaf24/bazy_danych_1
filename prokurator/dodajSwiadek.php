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
        $imieS = test_input($_POST['imieS']);
        $nazwiskoS = $_POST['nazwiskoS'];

        $_SESSION['sprawa_id'] = $_POST['sprawa_id'];
        $_SESSION['imieS'] = $_POST['imieS'];
        $_SESSION['nazwiskoS'] = $_POST['nazwiskoS'];
    }
    
    $imieS = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ]+/", "", $imieS);
    $nazwiskoS = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ]+/", "", $nazwiskoS);


    if ($imieS != $_POST['imieS'] || strlen($imieS) < 2) {
        $_SESSION['imieSERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }

    if ($nazwiskoS != $_POST['nazwiskoS'] || strlen($nazwiskoS) < 2) {
        $_SESSION['nazwiskoSERROR'] = "Za którkie, lub niedozwolone znaki!";
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

        //Dodawanie świadka, pierwsze sprawdzam czy takiego już nie ma dla tego przestępstwa
        $quest = "SELECT * FROM kartoteka.swiadek s WHERE s.imie = '$imieS' AND s.nazwisko = '$nazwiskoS' AND s.przestepstwo_id = $sprawa_id;";
        $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
            else {
                $tab = pg_fetch_all($val);
            }
            
        if($tab[0]['id']){  //jeżeli jest już taki świadek
            $_SESSION['imieSERROR'] ="Świadek o tym imieniu i nazwisku już jest w tej sprawie!";
        }
        else{
            $quest = "INSERT INTO kartoteka.swiadek (imie, nazwisko, przestepstwo_id) 
                        VALUES  ('$imieS', '$nazwiskoS', $sprawa_id);";
            $val = pg_query($db, $quest);
            if(!$val) {
                 $_SESSION['error']= " Błąd serwera! ".$quest;
                 $flag = false;
            } 
        }

        if($flag) {
            //session_destroy();
            $_SESSION['error'] = "Dodano świadka!";
            $_SESSION['imieS'] ="";
            $_SESSION['nazwiskoS'] ="";
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
