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
        $typ_mod = test_input($_POST['typ_mod']);
        $motyw_mod = test_input($_POST['motyw_mod']);
        $data_przestepstwa = $_POST['data_przestepstwa'];
        $miasto_mod = test_input($_POST['miasto_mod']);
        $kraj_mod = test_input($_POST['kraj_mod']);
        $przestepstwo_id_mod = test_input($_POST['przestepstwo_id_mod']);

        $_SESSION['typ_mod'] = $_POST['typ_mod'];
        $_SESSION['motyw_mod'] = $_POST['motyw_mod'];
        $_SESSION['data_przestepstwa'] = $_POST['data_przestepstwa'];
        $_SESSION['miasto_mod'] = $_POST['miasto_mod'];
        $_SESSION['kraj_mod'] = $_POST['kraj_mod'];
        $_SESSION['przestepstwo_id_mod'] = $_POST['przestepstwo_id_mod'];
    }
    
    $typ_mod = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $typ_mod);
    $motyw_mod = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $motyw_mod);
    $numer_dowodu = preg_replace("/[^0-9]+/", "", $numer_dowodu);
    $miasto_mod = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $miasto_mod);
    $kraj_mod = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $kraj_mod);


    if ($typ_mod != $_POST['typ_mod'] || strlen($typ_mod) < 2) {
        $_SESSION['typ_modERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }
    if ($motyw_mod != $_POST['motyw_mod']) {
        $_SESSION['motyw_modERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }

    if (empty($motyw_mod)) {
        $motyw_mod = "nieznany/brak";
    }

    if ($miasto_mod != $_POST['miasto_mod'] || strlen($miasto_mod) < 2) {
        $_SESSION['miasto_modERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }

    if ($kraj_mod != $_POST['kraj_mod'] || strlen($kraj_mod) < 2) {
        $_SESSION['kraj_modERROR'] = "Za którkie, lub niedozwolone znaki!";
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

        //Dodaje miejsce
        $quest = "SELECT * FROM kartoteka.miejsce_przestepstwa m WHERE m.miasto = '$miasto_mod' AND m.kraj='$kraj_mod';";
        $val = pg_query($db, $quest);
        if(!$val) {
            $_SESSION['error'] ="Błąd serwera! ".$quest;
            $flag = false;
        } 
        else {
            $tab = pg_fetch_all($val);
        }

        if($tab[0]['id'] == null){
            $quest = "INSERT INTO kartoteka.miejsce_przestepstwa (kraj, miasto) 
                        VALUES ('$kraj_mod', '$miasto_mod');";
            $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
        }

        //znajduje dodane ID miejsca i zapamietuje
        $quest = "SELECT * FROM kartoteka.miejsce_przestepstwa m WHERE m.miasto = '$miasto_mod' AND m.kraj='$kraj_mod';";
        $val = pg_query($db, $quest);
        if(!$val) {
            $_SESSION['error'] ="Błąd serwera! ".$quest;
            $flag = false;
        } 
        else {
            $tab = pg_fetch_all($val);
        }

        $miejsce_id = $tab[0]['id'];

        //sprawdzam czy nie to samo
        $quest = "SELECT * FROM kartoteka.przestepstwo m WHERE m.id = $przestepstwo_id_mod;";
        $val = pg_query($db, $quest);
        if(!$val) {
            $_SESSION['error'] ="Błąd serwera! ".$quest;
            $flag = false;
        } 
        else {
            $tab = pg_fetch_all($val);
        }

        if(($tab[0]['typ'] == $typ_mod) && ($tab[0]['motyw'] == $motyw_mod) && ($tab[0]['data_przestepstwa'] == $data_przestepstwa) && ($tab[0]['miejsce_id'] == $miejsce_id)){
            $_SESSION['typ_modERROR'] ="Nic nie zostało zmienione!";
            $flag = false;
        }
        else{        //UPDATE przestepstwo
            $quest = "UPDATE kartoteka.przestepstwo  SET 
                                                    typ = '$typ_mod',
                                                    motyw = '$motyw_mod',
                                                    data_przestepstwa = '$data_przestepstwa',
                                                    miejsce_id = $miejsce_id
                                                    WHERE
                                                    id = $przestepstwo_id_mod;";
            $val = pg_query($db, $quest);
            if(!$val) {
                    $_SESSION['error']= " Błąd serwera! ".$quest;
                    $flag = false;
            } 
        }
        if($flag) {
            //session_destroy();
            $_SESSION['error'] = "Update przestępstwo!";
            $_SESSION['typ_mod'] = "";
            $_SESSION['motyw_mod'] = "";
            $_SESSION['miasto_mod'] = "";
            $_SESSION['kraj_mod'] ="";
            $_SESSION['przestepstwo_id_mod'] = "";
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
