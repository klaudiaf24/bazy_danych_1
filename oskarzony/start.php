<?php
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['pass'] = $_POST['pass'];
    }
    $email = $_SESSION['email'];
    $haslo = $_SESSION['pass'];

    $dbname      = "dbname = u7fil";
    $credentials = "user = u7fil password = 7fil";
 
    $db = pg_connect( "$dbname $credentials");
    if(!$db) {
        $_SESSION['error'] = "Brak połączenia z bazą danch!";
    }

    //WYZNACZANIE MOICH DANYCH
    $quest = "SELECT * FROM kartoteka.moje_dane_oskarzony ('$email' , '$haslo');";
    $me = pg_query($db, $quest);

    if(!$me) 
        $_SESSION['error'] = "Błąd serwera! ".$quest;
    else {
        $tab = pg_fetch_all($me);
            if($tab[0]['id'] === null) {
               $_SESSION['error'] = "Oskarzony o tym e-mail nie istnieje!";
               exit();
            }
            else{
                $id = $_SESSION['id'] = $tab[0]['id'];
                $_SESSION['imie'] = $tab[0]['imie'];
                $_SESSION['nazwisko'] = $tab[0]['nazwisko'];
                $ida =$_SESSION['adwokat_id'] = $tab[0]['adwokat_id'];
                $idp = $_SESSION['prokurator_id'] = $tab[0]['prokurator_id'];
               }
       }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Akta Oskarżonych</title>
        <link rel="stylesheet" href="../css/style1.css"> 

        <script type="text/javascript">
            var firsttime = true;
            window.onload = function(){
                if(firsttime){ 
                    show(' ');
                    firsttime = false;
                    }
            }

            function show(whichID){
                document.getElementById("tab1").style.display='none';
                document.getElementById("tab2").style.display='none';
                document.getElementById("tab3").style.display='none';
                document.getElementById(whichID).style.display='block'
            }
        </script>
    </head>
    <body>
        <div id="dane"> 
        <a href="../logowanie/wyloguj.php">Wyloguj</a>
            <?php echo '<h3>Witaj</h3><h4>';
            echo $_SESSION['imie']; 
            echo ' '.$_SESSION['nazwisko']; 
            echo "</h4><br/><br/>";
            ?>

        </div>
        <div id="menu">
            <input class="button" type="button" onclick="show('tab1')" value="Wyświetl dane mojego adwokata" ><br />
            <input class="button" type="button" onclick="show('tab2')" value="Sprawdź moje sprawy" ><br />
            <input class="button" type="button" onclick="show('tab3')" value="Sprawdź wyroki" ><br />
        </div>

        <div id="ekran">
            <div id="tab1">
                <table style="text-align: left; ">
                    <?php
                        $quest = "SELECT * FROM kartoteka.adwokat WHERE id = $ida;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab = pg_fetch_all($val);

                        echo "<tr>";
                        echo "<td>Imię:</td><td>".$tab[0]['imie']."</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Nazwisko:</td><td>".$tab[0]['nazwisko']."</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Nazwa kancelarii:</td><td>".$tab[0]['nazwa_kancelarii']."</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo " <td>Numer licencji:</td><td>".$tab[0]['numer_licencji']."</td>";
                        echo "</tr>";
                        
                        
                    ?>
                </table>
            </div>
            <div id="tab2">
                <table>
                <thead>
                    <tr>
                        <th>Zdarzenie</th>
                        <th>Motyw</th>
                        <th>Data</th>
                        <th>Miejsce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $quest = "SELECT * FROM kartoteka.wszystkie_moje_przestepstwa_oskarzony ($id)";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $przestepstwo_tab = pg_fetch_all($val);
                    
                    foreach($przestepstwo_tab as $element) {
                        $tempID = $element['id'];
                        $tempID_miejsce = $element['miejsce_id'];

                        //miejsce
                        $quest = "SELECT * FROM kartoteka.miejsce_przestepstwa m
                                        WHERE m.id = $tempID_miejsce;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab = pg_fetch_all($val);

                        $miejsce_info = $tab[0]['miasto'].", ".$tab[0]['kraj'];

                       
                        echo "<tr>";
                        echo "<td>".$element['typ']."</td>";
                        echo "<td>".$element['motyw']."</td>";
                        echo "<td>".$element['data_przestepstwa']."</td>";
                        echo "<td>".$miejsce_info."</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
                </table>
            </div>
            <div id="tab3">
                <table>
                <thead>
                    <tr>
                        <th>Sprawa</th>
                        <th>Data przestępstwa</th>
                        <th>Miejsce</th>
                        <th>Wyrok</th>
                        <th>Klasyfikacja wyroku</th>
                        <th>Więzienie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $quest = "SELECT * FROM kartoteka.oskarzony_przestepstwo op WHERE op.oskarzony_id = ($id)";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $tab = pg_fetch_all($val);

                    
                    foreach($tab as $element) {
                        $wyrok_id = $element['wyrok_id'];
                        $przestepstwo_id = $element['przestepstwo_id'];

                        //wyrok
                        $quest = "SELECT * FROM kartoteka.wyrok w
                                        WHERE w.id = $wyrok_id;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab_wyrok = pg_fetch_all($val);

                        //przestepstwo
                        $quest = "SELECT * FROM kartoteka.przestepstwo w
                                    WHERE w.id = $przestepstwo_id;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab_przes = pg_fetch_all($val);

                        //miejsce
                        $temp = $tab_przes[0]['miejsce_id'];
                        $quest = "SELECT * FROM kartoteka.miejsce_przestepstwa m
                                    WHERE m.id = $temp;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $miejsce = pg_fetch_all($val);
                        
                        $miejsce_info = $miejsce[0]['miasto'].", ".$miejsce[0]['kraj'];

                        //wiezienie
                        $temp = $tab_wyrok[0]['wiezienie_id'];
                        $quest = "SELECT * FROM kartoteka.wiezienie w
                                    WHERE w.id = $temp;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $wiezienie = pg_fetch_all($val);
                        
                        $wiezienie_info = $wiezienie[0]['miasto'].", ".$wiezienie[0]['kraj']." - ".$wiezienie[0]['typ'];
                        if($temp==0) $wiezienie_info = "Brak";

                        echo "<tr>";
                        echo "<td>".$tab_przes[0]['typ']."</td>";
                        echo "<td>".$tab_przes[0]['data_przestepstwa']."</td>";
                        echo "<td>".$miejsce_info."</td>";
                        echo "<td>".$tab_wyrok[0]['status_winy']."</td>";
                        echo "<td>".$tab_wyrok[0]['klasyfikacja']."</td>";
                        echo "<td>".$wiezienie_info."</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
                </table>
            </div>
        
        </div>
        <div style="clear:both;"></div>
        
    </body>

</html>
