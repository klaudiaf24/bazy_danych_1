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
        $imieP = test_input($_POST['imieP']);
        $nazwiskoP = test_input($_POST['nazwiskoP']);
        $straty = test_input($_POST['straty']);

        $_SESSION['sprawa_id'] = $_POST['sprawa_id'];
        $_SESSION['straty'] = $_POST['straty'];
        $_SESSION['imieP'] = $_POST['imieP'];
        $_SESSION['nazwiskoP'] = $_POST['nazwiskoP'];
    }
    
    $imieP = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ]+/", "", $imieP);
    $nazwiskoP = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ]+/", "", $nazwiskoP);
    switch ($straty) {
        case 1:{ $straty = "Materialne"; break;}
        case 2:{ $straty = "Lekki uszczerbek na zdrowiu"; break;}
        case 3:{ $straty = "Uszczerbek na zdrowiu"; break;}
        case 4:{ $straty = "Znaczący uszczerbek na zdrowiu"; break;}
        case 5:{ $straty = "Śmierć"; break;}
    }


    if ($imieP != $_POST['imieP'] || strlen($imieP) < 2) {
        $_SESSION['imiePERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }

    if ($nazwiskoP != $_POST['nazwiskoP'] || strlen($nazwiskoP) < 2) {
        $_SESSION['nazwiskoPERROR'] = "Za którkie, lub niedozwolone znaki!";
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

        //Dodawanie poszkodowanego, pierwsze sprawdzam czy takiego już nie ma dla tego przestępstwa
        $quest = "SELECT * FROM kartoteka.poszkodowany s WHERE s.imie = '$imieP' AND s.nazwisko = '$nazwiskoP' AND s.przestepstwo_id = $sprawa_id;";
        $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
            else {
                $tab = pg_fetch_all($val);
            }
            
        if($tab[0]['id']){  //jeżeli jest już taki świadek
            $_SESSION['imiePERROR'] ="Poszkodowany o tym imieniu i nazwisku już jest w tej sprawie!";
        }
        else{
            $quest = "INSERT INTO kartoteka.poszkodowany (imie, nazwisko, straty, przestepstwo_id) 
                        VALUES  ('$imieP', '$nazwiskoP', '$straty',$sprawa_id);";
            $val = pg_query($db, $quest);
            if(!$val) {
                 $_SESSION['error']= " Błąd serwera! ".$quest;
                 $flag = false;
            } 
        }

        if($flag) {
            //session_destroy();
            $_SESSION['error'] = "Dodano poszkodowanego!";
            $_SESSION['imieP'] ="";
            $_SESSION['nazwiskoP'] ="";
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
