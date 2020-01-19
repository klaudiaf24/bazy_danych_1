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
        $adwokat_id = test_input($_POST['adwokat_id']);
        $imieO = test_input($_POST['imieO']);
        $nazwiskoO = test_input($_POST['nazwiskoO']);
        $przestepstwo_id_FO = test_input($_POST['przestepstwo_id_FO']);
        $id_prokuratora = $_POST['id']; //ID prokuratora
        $status_winy = test_input($_POST['status_winy']);
        $klasyfikacja = test_input($_POST['klasyfikacja']);
        $wiezienie_id = test_input($_POST['wiezienie_id']);

        $_SESSION['sprawca_id'] = $_POST['sprawca_id'];
        $_SESSION['status_winy'] = $_POST['status_winy'];
        $_SESSION['klasyfikacja'] = $_POST['klasyfikacja'];
        $_SESSION['wiezienie_id'] = $_POST['wiezienie_id'];
        $_SESSION['adwokat_id'] = $_POST['adwokat_id'];
        $_SESSION['id'] = $_POST['id'];
        $_SESSION['przestepstwo_id_FO'] = $_POST['przestepstwo_id_FO'];
        $_SESSION['imieO'] = $_POST['imieO'];
        $_SESSION['nazwiskoO'] = $_POST['nazwiskoO'];
    }
    
    $imieO = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $imieO);
    $nazwiskoO = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $nazwiskoO);
    

    if ($imieO != $_POST['imieO'] || strlen($imieO) < 2) {
        $_SESSION['imieOERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }

    if ($nazwiskoO != $_POST['nazwiskoO'] || strlen($nazwiskoO) < 2) {
        $_SESSION['nazwiskoOERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }

    switch ($klasyfikacja) {
        case 1:{ $klasyfikacja = 'Brak'; break;}
        case 2:{ $klasyfikacja = 'Kara pieniężna'; break;}
        case 3:{ $klasyfikacja = 'Wyrok w zawieszeniu'; break;}
        case 4:{ $klasyfikacja = 'Kara więzienna lekka - do 1 roku'; break;}
        case 5:{ $klasyfikacja = 'Kara więzienna średnia - od 1 roku - 10 lat'; break;}
        case 6:{ $klasyfikacja = 'Kara więzienna wysoka - od 10 lat'; break;}
        case 7:{ $klasyfikacja = 'Kara śmierci'; break;}
    }

    switch ($status_winy) {
        case 1:{ $status_winy = 'winny'; break;}
        case 2:{ $status_winy = 'niewinny'; break;}
        case 3:{ $status_winy = 'nie określono'; break;}
    }

    if($status_winy != 'winny'){ 
        if($klasyfikacja != 'Brak'){
            $_SESSION['kalsyfikacjaERROR'] = "Oskarżony nie jest winny, więc wciąż brak wyroku!";
            $flag = false;
        }
        if($wiezienie_id != 0){
            $_SESSION['wiezienieERROR'] = "Oskarżony nie jest winny, więc nie może zostać osadzony w więzieniu!";
            $flag = false;
        }
        $wiezienie_id = 0;
    }  
    else{   //kiedy jest winny
        if($klasyfikacja == 'Brak'){
            $_SESSION['kalsyfikacjaERROR'] = "Oskarżony jest winny, zbór coś z tym!";
            $flag = false;
        }
        if(     (($klasyfikacja == 'Kara pieniężna') || ($klasyfikacja == 'Wyrok w zawieszeniu')) && ($wiezienie_id != 0)       )   {
            $_SESSION['wiezienieERROR'] = "Klasyfikacja wyroku nie pozwala na osadzenie oskarżonego w więzieniu!";
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

        //Dodawanie oskarzonego ale pierwsze sprawdzamy czy takiego nie ma
        $quest = "SELECT * FROM kartoteka.oskarzony s WHERE s.imie = '$imieO' AND s.nazwisko = '$nazwiskoO' AND s.adwokat_id = $adwokat_id;";
        $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
            else {
                $tab = pg_fetch_all($val);
            }
            
        if($tab[0]['id']){  //jeżeli jest już taki oskarzony
            $_SESSION['imieOERROR'] ="Oskarżony o tym imieniu i nazwisku już jest w tej sprawie!";
            $flag = false;
        }
        else{
            $quest = "INSERT INTO kartoteka.oskarzony (imie, nazwisko, adwokat_id, prokurator_id) 
                        VALUES  ('$imieO', '$nazwiskoO', $adwokat_id, $id_prokuratora);";
            $val = pg_query($db, $quest);
            if(!$val) {
                 $_SESSION['error']= " Błąd serwera! ".$quest;
                 $flag = false;
            } 

            //znajdujemy id dodanego oskarzonego
            $quest = "SELECT * FROM kartoteka.ostatni_oskarzony;";
            $val = pg_query($db, $quest);
                if(!$val) {
                    $_SESSION['error'] ="Błąd serwera! ".$quest;
                    $flag = false;
                } 
                else {
                    $tab = pg_fetch_all($val);
                }
            $osk_id_new = $tab[0]['id']; 

            //dodajemy wyrok oskarzonego
            $quest = "INSERT INTO kartoteka.wyrok (status_winy, klasyfikacja, wiezienie_id) 
                        VALUES  ('$status_winy', '$klasyfikacja', $wiezienie_id);";
            $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error']= " Błąd serwera! ".$quest;
                $flag = false;
           } 

           //szukamy dodanego wyroku
           $quest = "SELECT * FROM kartoteka.ostatni_wyrok ;";
            $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
            else {
                $tab = pg_fetch_all($val);
            }
            $nowy_wyrok = $tab[0]['id']; 

            //dodajemy do tabeli oskarzony_przestepstwa
            $quest = "INSERT INTO kartoteka.oskarzony_przestepstwo (oskarzony_id, przestepstwo_id, wyrok_id) 
                        VALUES  ($osk_id_new, $przestepstwo_id_FO, $nowy_wyrok);";
            $val = pg_query($db, $quest);
            if(!$val) {
                 $_SESSION['error']= " Błąd serwera! ".$quest;
                 $flag = false;
            } 

        }

        if($flag) {
            //session_destroy();
            $_SESSION['error'] = "Dodano poszkodowanego!";
            $_SESSION['imieO'] ="";
            $_SESSION['nazwiskoO'] ="";
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
