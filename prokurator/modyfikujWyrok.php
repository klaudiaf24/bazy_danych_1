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
        $numer_sprawy_modW =  test_input($_POST['numer_sprawy_modW']);
        $sprawca_id_modW = test_input($_POST['sprawca_id_modW']);
        $status_winy_modW = test_input($_POST['status_winy_modW']);
        $klasyfikacja_modW = test_input($_POST['klasyfikacja_modW']);
        $wiezienie_id_modW = test_input($_POST['wiezienie_id_modW']);

        $_SESSION['numer_sprawy_modW'] = $_POST['numer_sprawy_modW'];
        $_SESSION['sprawca_id_modW'] = $_POST['sprawca_id_modW'];
        $_SESSION['status_winy_modW'] = $_POST['status_winy_modW'];
        $_SESSION['klasyfikacja_modW'] = $_POST['klasyfikacja_modW'];
        $_SESSION['wiezienie_id_modW'] = $_POST['wiezienie_id_modW'];
       
    }

    $numer_sprawy_modW = preg_replace("/[^0-9]+/", "", $numer_sprawy_modW);
    if($numer_sprawy_modW != $_POST['numer_sprawy_modW']){
        $_SESSION['numer_sprawy_modW'] = "Numer składa się z cyfr!";
        $flag = false;
    }

    switch ($klasyfikacja_modW) {
        case 1:{ $klasyfikacja_modW = 'Brak'; break;}
        case 2:{ $klasyfikacja_modW = 'Kara pieniężna'; break;}
        case 3:{ $klasyfikacja_modW = 'Wyrok w zawieszeniu'; break;}
        case 4:{ $klasyfikacja_modW = 'Kara więzienna lekka - do 1 roku'; break;}
        case 5:{ $klasyfikacja_modW = 'Kara więzienna średnia - od 1 roku - 10 lat'; break;}
        case 6:{ $klasyfikacja_modW = 'Kara więzienna wysoka - od 10 lat'; break;}
        case 7:{ $klasyfikacja_modW = 'Kara śmierci'; break;}
    }

    switch ($status_winy_modW) {
        case 1:{ $status_winy_modW = 'winny'; break;}
        case 2:{ $status_winy_modW = 'niewinny'; break;}
        case 3:{ $status_winy_modW = 'nie określono'; break;}
    }

    if($status_winy_modW != 'winny'){ 
        if($klasyfikacja_modW != 'Brak'){
            $_SESSION['kalsyfikacja_modWERROR'] = "Oskarżony nie jest winny, więc wciąż brak wyroku!";
            $flag = false;
        }
        if($wiezienie_id_modW != 0){
            $_SESSION['wiezienie_modWERROR'] = "Oskarżony nie jest winny, więc nie może zostać osadzony w więzieniu!";
            $flag = false;
        }
    }  
    else{   //kiedy jest winny
        if($klasyfikacja_modW == 'Brak'){
            $_SESSION['kalsyfikacja_modWERROR'] = "Oskarżony jest winny, zbór coś z tym!";
            $flag = false;
        }
        if(     (($klasyfikacja_modW == 'Kara pieniężna') || ($klasyfikacja_modW == 'Wyrok w zawieszeniu')) && ($wiezienie_id_modW != 0)       )   {
            $_SESSION['wiezienie_modWERROR'] = "Klasyfikacja wyroku nie pozwala na osadzenie oskarżonego w więzieniu!";
            $flag = false;
        }
    }


    if($flag) {
        $dbname      = "dbname = u7fil";
        $credentials = "user = u7fil password=7fil";
     
        $db = pg_connect( "$dbname $credentials");
        if(!$db) {
            $_SESSION['error'] ="Brak połączenia z bazą danych\n";
            $flag = false;
        }

        //Sprawdzam czy to moje przestepstwo
        $quest = "  SELECT * FROM kartoteka.oskarzony_przestepstwo op 
                    WHERE op.przestepstwo_id = $numer_sprawy_modW AND op.oskarzony_id = $sprawca_id_modW;";
        $val = pg_query($db, $quest);
        if(!$val) {
            $_SESSION['error'] ="Błąd serwera! ".$quest;
            $flag = false;
        } 
        else {
            $tab = pg_fetch_all($val);
        }

        if($tab[0]['oskarzony_id'] != $sprawca_id_modW){
            $_SESSION['numer_sprawy_modWERROR']= 'Ten oskarżony nie ma wyroku w tej sprawie! Sprawdź "Moje prowadzone sprawy"!';
            $flag = false;
        }

        //Szukam wyroku
        $wyrok = $tab[0]['wyrok_id'];

        if($flag){
            //update wyroku
            $quest = "UPDATE kartoteka.wyrok SET 
                            status_winy= '$status_winy_modW',
                            klasyfikacja ='$klasyfikacja_modW',
                            wiezienie_id = $wiezienie_id_modW
                            WHERE id = $wyrok;";
            $val = pg_query($db, $quest);
            if(!$val) {
                    $_SESSION['error']= " Błąd serwera! ".$quest;
                    $flag = false;
            } 
                
            $_SESSION['error'] = "Uddate wyrok!";
            $_SESSION['numer_sprawy_modW'] = "";
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

