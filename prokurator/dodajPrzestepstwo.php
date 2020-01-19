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
        $typ = test_input($_POST['typ']);
        $motyw = test_input($_POST['motyw']);
        $data_przestepstwa = $_POST['data_przestepstwa'];
        $miasto = test_input($_POST['miasto']);
        $kraj = test_input($_POST['kraj']);
        $narzedzie = test_input($_POST['narzedzie']);
        $numer_dowodu = test_input($_POST['numer_dowodu']);
        $miejsce_przechowania = test_input($_POST['miejsce_przechowania']);

        $_SESSION['typ'] = $_POST['typ'];
        $_SESSION['motyw'] = $_POST['motyw'];
        $_SESSION['data_przestepstwa'] = $_POST['data_przestepstwa'];
        $_SESSION['miasto'] = $_POST['miasto'];
        $_SESSION['kraj'] = $_POST['kraj'];
        $_SESSION['narzedzie'] = $_POST['narzedzie'];
        $_SESSION['numer_dowodu'] = $_POST['numer_dowodu'];
        $_SESSION['miejsce_przechowania'] = $_POST['miejsce_przechowania'];
    }
    
    $typ = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $typ);
    $motyw = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $motyw);
    $numer_dowodu = preg_replace("/[^0-9]+/", "", $numer_dowodu);
    $miasto = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $miasto);
    $kraj = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $kraj);
    $narzedzie = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $narzedzie);
    $miejsce_przechowania = preg_replace("/[^a-zA-ZąćęłńóśźżĄĘŁŃÓŚŹŻ ]+/", "", $miejsce_przechowania);


    if ($typ != $_POST['typ'] || strlen($typ) < 2) {
        $_SESSION['typERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }
    if ($motyw != $_POST['motyw']) {
        $_SESSION['motywERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }

    if (empty($motyw)) {
        $motyw = "nieznany/brak";
    }

    if ($miasto != $_POST['miasto'] || strlen($miasto) < 2) {
        $_SESSION['miastoERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }
    if ($kraj != $_POST['kraj'] || strlen($kraj) < 2) {
        $_SESSION['krajERROR'] = "Za którkie, lub niedozwolone znaki!";
        $flag = false;
    }

    $czy_narzedzie = true;
    if(empty($narzedzie)){//narzedzia brak
        if (!empty($numer_dowodu) || !empty($miejsce_przechowania)) {
            $_SESSION['narzedzieERROR'] = "Przy braku dowodu nie wypełniaj dwóch poniższych pół!";
            $flag = false;
        }
        $czy_narzedzie = false;
    }
    else {   
        $czy_narzedzie = true;
        if($narzedzie != $_POST['narzedzie'] || strlen($narzedzie) < 2){
            $_SESSION['narzedzieERROR'] = "Za którkie, lub niedozwolone znaki!";
            $flag = false;
        }
        if($numer_dowodu != $_POST['numer_dowodu'] || strlen($numer_dowodu) != 5){
            $_SESSION['numer_dowoduERROR'] = "Numer dowodu składa się z 5 cyfr!";
            $flag = false;
        }
        if($miejsce_przechowania != $_POST['miejsce_przechowania'] || strlen($miejsce_przechowania) < 2){
            $_SESSION['miejsce_przechowaniaERROR'] =  "Za którkie, lub niedozwolone znaki!";
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

        if($czy_narzedzie){
            $flagx = true;
            //Dodaje miejsce
            $quest = "SELECT * FROM kartoteka.miejsce_przestepstwa m WHERE EXISTS m.miasto = '$miasto' AND m.kraj='$kraj';";
            $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
            else {
                $tab = pg_fetch_all($val);
            }
   
            if(!$tab[0]['id']){
                $quest = "INSERT INTO kartoteka.miejsce_przestepstwa (kraj, miasto) 
                            VALUES ('$kraj', '$miasto');";
                $val = pg_query($db, $quest);
                if(!$val) {
                    $_SESSION['error'] ="Błąd serwera! ".$quest;
                    $flagx = false;
                    $flag = false;
                } 
            }

            //znajduje dodane ID miejsca i zapamietuje
            $quest = "SELECT * FROM kartoteka.miejsce_przestepstwa m WHERE m.miasto = '$miasto' AND m.kraj='$kraj';";
            $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
            else {
                $tab = pg_fetch_all($val);
           }
   
            $miejsce_id = $tab[0]['id'];

            //Dodaje przestepstwo
            $quest = "INSERT INTO kartoteka.przestepstwo (typ, motyw, data_przestepstwa, miejsce_id) 
                        VALUES  ('$typ', '$motyw', '$data_przestepstwa', $miejsce_id);";
            $val = pg_query($db, $quest);
            if(!$val) {
                 $_SESSION['error']= " Błąd serwera! ".$quest;
                 $flagx = false;
                 $flag = false;
            } 
            
            //znajduje numer id tego przestepstwa
            $quest = "SELECT * FROM kartoteka.przestepstwo p WHERE p.typ = '$typ' AND p.motyw ='$motyw' AND p.data_przestepstwa = '$data_przestepstwa';";
            $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
            else {
                $tab = pg_fetch_all($val);
            }
   
            $prze_id = $tab[0]['id'];

            //Dodaje narzedzie
            //sprawdzam czy numer dowodu sie nie duplikuje
            $quest = "SELECT * FROM kartoteka.narzedzie n WHERE n.numer_dowodu = $numer_dowodu";
            $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
            else {
                $tab = pg_fetch_all($val);
            }
   
            if($tab[0]['id']){ //jeżeli istnieje taki numer
                $_SESSION['numer_dowoduERROR'] ="Podany numer dowodu już istnieje!";
                $flag = false;
                $flagx = false;
            }
            else{
                $quest = "INSERT INTO kartoteka.narzedzie (narzedzie, numer_dowodu, miejsce_przechowania, przestepstwo_id) 
                            VALUES  ('$narzedzie', $numer_dowodu, '$miejsce_przechowania', $prze_id);";
                $val = pg_query($db, $quest);
                if(!$val) {
                    $_SESSION['error']= " Błąd serwera! ".$quest;
                    $flagx = false;
                    $flag = false;
                } 
            }
            if($flagx) {
                //session_destroy();
                $_SESSION['error'] = "Dodano przestępstwo!";
                $_SESSION['typ'] = "";
                $_SESSION['motyw'] = "";
                $_SESSION['miasto'] = "";
                $_SESSION['kraj'] ="";
                $_SESSION['narzedzie'] = "";
                $_SESSION['numer_dowodu'] = "";
                $_SESSION['miejsce_przechowania'] = "";
                header ('Location: start.php');   
            }
            else {
                
                header ('Location: start.php'); 
            }
    
        }
        else{   //bez narzedzia
            $flagx = true;
            //Dodaje miejsce
            //SPRAWDZAM CZY NIE MA JUŻ TAKIEGO 
            $quest = "SELECT * FROM kartoteka.miejsce_przestepstwa m WHERE m.miasto = '$miasto' AND m.kraj='$kraj';";
            $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
            else {
                $tab = pg_fetch_all($val);
            }
   
            if(!$tab[0]['id']){
                $quest = "INSERT INTO kartoteka.miejsce_przestepstwa (kraj, miasto) 
                            VALUES ('$kraj', '$miasto');";
                $val = pg_query($db, $quest);
                if(!$val) {
                    $_SESSION['error'] ="Błąd serwera! ".$quest;
                    $flagx = false;
                    $flag = false;
                } 
            }
            //znajduje dodane ID miejsca i zapamietuje
            $quest = "SELECT * FROM kartoteka.miejsce_przestepstwa m WHERE m.miasto = '$miasto' AND m.kraj='$kraj';";
            $val = pg_query($db, $quest);
            if(!$val) {
                $_SESSION['error'] ="Błąd serwera! ".$quest;
                $flag = false;
            } 
            else {
                $tab = pg_fetch_all($val);
            }
   
            $miejsce_id = $tab[0]['id'];

            //Dodaje przestepstwo
            $quest = "INSERT INTO kartoteka.przestepstwo (typ, motyw, data_przestepstwa, miejsce_id) 
                        VALUES  ('$typ', '$motyw', '$data_przestepstwa', $miejsce_id);";
            $val = pg_query($db, $quest);
            if(!$val) {
                 $_SESSION['error'] ="Błąd serwera! ".$quest;
                 $flagx = false;
                 $flag = false;
            } 
            
            if($flagx) {
                //session_destroy();
                $_SESSION['error'] = "Dodano przestępstwo!";
                $_SESSION['typ'] = "";
                $_SESSION['motyw'] = "";
                $_SESSION['miasto'] = "";
                $_SESSION['kraj'] ="";
                $_SESSION['narzedzie'] = "";
                $_SESSION['numer_dowodu'] = "";
                $_SESSION['miejsce_przechowania'] = "";
                header ('Location: start.php');   
            }
            else {
                
                header ('Location: start.php'); 
            }

        }
        pg_close($db);
    }
    else {
        header ('Location: start.php'); 
    }
    
?>
