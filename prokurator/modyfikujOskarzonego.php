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
        $adwokat_id_mod_osk = test_input($_POST['adwokat_id_mod_osk']);
        $imieO_mod = test_input($_POST['imieO_mod']);
        $nazwiskoO_mod = test_input($_POST['nazwiskoO_mod']);
        $oskarzony_id = test_input($_POST['oskarzony_id']);

        $_SESSION['oskarzony_id'] = $_POST['oskarzony_id'];
        $_SESSION['adwokat_id_mod_osk'] = $_POST['adwokat_id_mod_osk'];
        $_SESSION['imieO_mod'] = $_POST['imieO_mod'];
        $_SESSION['nazwiskoO_mod'] = $_POST['nazwiskoO_mod'];
    }
    
    $imieO_mod = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $imieO_mod);
    $nazwiskoO_mod = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $nazwiskoO_mod);
    

    if ($imieO_mod != $_POST['imieO_mod'] || strlen($imieO_mod) < 2) {
        $_SESSION['imieO_modERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }

    if ($nazwiskoO_mod != $_POST['nazwiskoO_mod'] || strlen($nazwiskoO_mod) < 2) {
        $_SESSION['nazwiskoO_modERROR'] = "Za którkie, lub niedozwolone znaki!";
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

        $quest = "SELECT * FROM kartoteka.oskarzony WHERE id = $oskarzony_id;";
        $val = pg_query($db, $quest);
        if(!$val) {
            $_SESSION['error']= " Błąd serwera! ".$quest;
            $flag = false;
        } 
        else {
            $tab = pg_fetch_all($val);
            if(($tab[0]['imie'] == $imieO_mod) && ($tab[0]['nazwisko'] == $nazwiskoO_mod) && ($tab[0]['adwokat_id'] == $adwokat_id_mod_osk) ){
                $flag = false;
                $_SESSION['imieO_modERROR']= " Przecież nic nie zostało zmienione!";
            }   
        }
        $quest = "UPDATE kartoteka.oskarzony    SET 
                                                imie = '$imieO_mod',
                                                nazwisko = '$nazwiskoO_mod',
                                                adwokat_id = $adwokat_id_mod_osk
                                                WHERE
                                                id = $oskarzony_id;";
        $val = pg_query($db, $quest);
        if(!$val) {
            $_SESSION['error']= " Błąd serwera! ".$quest;
            $flag = false;
        } 
        if($flag) {      


            //session_destroy();
            $_SESSION['error'] = "Update oskarżonego udany!";
            $_SESSION['imieO_mod'] ="";
            $_SESSION['nazwiskoO_mod'] ="";
            header ('Location: start.php');   
        }
        else {
            header ('Location: start.php'); 
        }
        pg_close($db);
    }
    else {
        header ('Location: start.php'); 
    }
    
?>
