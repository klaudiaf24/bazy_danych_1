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
    $quest = "SELECT * FROM kartoteka.moje_dane_adwokat ('$email' , '$haslo');";
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
               }
       }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Kandelaria prawna</title>
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
                document.getElementById("tab4").style.display='none';
                document.getElementById("tab5").style.display='none';
                document.getElementById("tab6").style.display='none';
                document.getElementById(whichID).style.display='block'
            }
        </script>
    </head>
    <body>
        <div id="dane"> 
        <a href="../logowanie/wyloguj.php">Wyloguj</a>
            <?php echo '<h3>Kancelaria Adwokacka '.$_SESSION['nazwa_kancelarii'].'</h3><h4>';
            echo $_SESSION['imie']; 
            echo ' '.$_SESSION['nazwisko']; 
            echo "</h4><h5 style='margin-left: 60px; font-size: 18px;'>Numer licencji: ".$_SESSION['numer_licencji']."</h5><br/><br/>";
            ?>

        </div>
        <div id="menu">
            <input class="button" type="button" onclick="show('tab1')" value="Wyświetl klientów" ><br />
            <input class="button" type="button" onclick="show('tab2')" value="Sprawdź sprawy" ><br />
            <input class="button" type="button" onclick="show('tab3')" value="Sprawdź dowody" ><br />
            <input class="button" type="button" onclick="show('tab4')" value="Sprawdź poszkodowanych" ><br />
            <input class="button" type="button" onclick="show('tab5')" value="Sprawdź świadków" ><br />
            <input class="button" type="button" onclick="show('tab6')" value="Statystyki rozpraw" ><br />

        </div>

        <div id="ekran">
            <div id="tab1">
                <table>
                <thead>
                    <tr>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Przestępstwo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $quest = "SELECT * FROM kartoteka.moi_oskarzeni_adwokat($id);";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab = pg_fetch_all($val);

                        foreach($tab as $element){
                            echo "<tr>";
                            echo "<td>".$element['imie']."</td>";
                            echo "<td>".$element['nazwisko']."</td>";

                            $tempid = $element['id'];
                            $quest = "SELECT * FROM kartoteka.wszystkie_moje_przestepstwa_oskarzony($tempid)";
                            $val = pg_query($db, $quest);
                            if(!$val) 
                                $_SESSION['error'] = "Błąd serwera! ".$quest;
                            else 
                                $przestepstwo_tab = pg_fetch_all($val);
                            
                            echo "<td>";
                            foreach($przestepstwo_tab as $przest) {
                                echo $przest['typ']."<br/>";
                            } 
                            echo "</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
                </table>
            </div>
            <div id="tab2">
                <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Zdarzenie</th>
                        <th>Motyw</th>
                        <th>Data</th>
                        <th>Miejsce</th>
                        <th>Oskarżony</th>
                        <th>Wyrok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $quest = "SELECT * FROM kartoteka.moje_sprawy_adwokat($id) ORDER BY id";
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

                        //oskarzony
                        $quest = "SELECT * FROM kartoteka.oskarzony o
                                        WHERE o.id IN ( SELECT oskarzony_id 
                                                        FROM kartoteka.oskarzony_przestepstwo op
                                                        WHERE op.przestepstwo_id = $tempID);";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab_osk = pg_fetch_all($val);

                        
                       
                        echo "<tr>";
                        echo "<td>".$element['id']."</td>";
                        echo "<td>".$element['typ']."</td>";
                        echo "<td>".$element['motyw']."</td>";
                        echo "<td>".$element['data_przestepstwa']."</td>";
                        echo "<td>".$miejsce_info."</td>";
                        echo "<td>";
                        foreach($tab_osk AS $osk){
                            echo $osk['imie']." ".$osk['nazwisko']."<br/>";
                            $oskID = $osk['id'];
                        //WYROK
                        $quest = "SELECT * FROM kartoteka.wyrok w
                                        WHERE w.id = ( SELECT wyrok_id 
                                                        FROM kartoteka.oskarzony_przestepstwo op
                                                        WHERE op.przestepstwo_id = $tempID AND op.oskarzony_id = $oskID);";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab_wyrok = pg_fetch_all($val);
                            echo "<td>".$tab_wyrok[0]['status_winy'].", ".$tab_wyrok[0]['klasyfikacja']."</td>";
                        }
                        echo "</td>";
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
                        <th>ID sprawy</th>
                        <th>Numer dowodu</th>
                        <th>Dowód</th>
                        <th>Miejsce przechowywania</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $quest = "SELECT * FROM kartoteka.moje_sprawy_adwokat($id) ORDER BY id;";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $tab = pg_fetch_all($val);

                    
                    foreach($tab as $prze) {
                        $przestepstwo_id = $prze['id'];
                        
                        $quest = "SELECT * FROM kartoteka.narzedzia_w_przestepstwie($przestepstwo_id) ORDER BY numer_dowodu;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab_narz = pg_fetch_all($val);
                        
                        if($tab_narz[0]){
                        echo "<tr>";
                        echo "<td>".$przestepstwo_id."</td>";
                        echo "<td>";
                        foreach($tab_narz as $narz) 
                          echo $narz['numer_dowodu']."<br/>";
                        echo "</td>";

                        echo "<td>";
                        foreach($tab_narz as $narz) 
                          echo $narz['narzedzie']."<br/>";
                        echo "</td>";

                        echo "<td>";
                        foreach($tab_narz as $narz) 
                          echo $narz['miejsce_przechowania']."<br/>";
                        echo "</td>";
                        echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
                </table>
            </div>
            <div id="tab4">
                <table>
                <thead>
                    <tr>
                        <th>ID sprawy</th>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Szkody</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $quest = "SELECT * FROM kartoteka.moje_sprawy_adwokat($id) ORDER BY id;";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $tab = pg_fetch_all($val);

                    
                    foreach($tab as $prze) {
                        $przestepstwo_id = $prze['id'];

                        $quest = "SELECT * FROM kartoteka.poszkodowany p WHERE p.przestepstwo_id = $przestepstwo_id;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab_narz = pg_fetch_all($val);

                        if($tab_narz[0]){    
                        echo "<tr>";
                        echo "<td>".$przestepstwo_id."</td>";
                        echo "<td>";
                        foreach($tab_narz as $narz) 
                          echo $narz['imie']."<br/>";
                        echo "</td>";

                        echo "<td>";
                        foreach($tab_narz as $narz) 
                          echo $narz['nazwisko']."<br/>";
                        echo "</td>";

                        echo "<td>";
                        foreach($tab_narz as $narz) 
                          echo $narz['straty']."<br/>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    }
                    ?>
                </tbody>
                </table>
            </div>
            <div id="tab5">
                <table>
                <thead>
                    <tr>
                        <th>ID sprawy</th>
                        <th>Imie</th>
                        <th>Nazwisko</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $quest = "SELECT * FROM kartoteka.moje_sprawy_adwokat($id) ORDER BY id;";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $tab = pg_fetch_all($val);

                    
                    foreach($tab as $prze) {
                        $przestepstwo_id = $prze['id'];

                        $quest = "SELECT * FROM kartoteka.swiadek s WHERE s.przestepstwo_id = $przestepstwo_id;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab_narz = pg_fetch_all($val);

                        if($tab_narz[0]['imie']){
                        echo "<tr>";
                        echo "<td>".$przestepstwo_id."</td>";                       
                        echo "<td>";
                        foreach($tab_narz as $narz) 
                          echo $narz['imie']."<br/>";
                        echo "</td>";

                        echo "<td>";
                        foreach($tab_narz as $narz) 
                          echo $narz['nazwisko']."<br/>";
                        echo "</td>";

                        echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
                </table>
            </div>
            <div id="tab6">
                <table>
                    <?php
                        $quest = "SELECT COUNT(*) FROM kartoteka.wyroki_w_sprawach_adwokat($id);";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab = pg_fetch_all($val);
                        
                        $ilosc_spraw = $tab[0]['count'];

                        $quest = "SELECT COUNT(*) FROM kartoteka.wyroki_w_sprawach_adwokat($id) WHERE status_winy='winny';";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab = pg_fetch_all($val);

                        $przegrane = $tab[0]['count']/$ilosc_spraw * 100;

                        $quest = "SELECT COUNT(*) FROM kartoteka.wyroki_w_sprawach_adwokat($id) WHERE status_winy='niewinny';";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab = pg_fetch_all($val);

                        $wygrane = $tab[0]['count']/$ilosc_spraw * 100;

                        $quest = "SELECT COUNT(*) FROM kartoteka.wyroki_w_sprawach_adwokat($id) WHERE status_winy='nie określono';";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab = pg_fetch_all($val);

                        $wtoku = $tab[0]['count']/$ilosc_spraw * 100;

                        $quest = "  SELECT *
                                    FROM kartoteka.staty_wyroki_adwokat($id)
                                    ORDER BY total LIMIT 1;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $tab = pg_fetch_all($val);

                        $najrz = $tab[0]['klas'].": ".$tab[0]['total'];

                        $quest = "  SELECT *
                                    FROM kartoteka.staty_wyroki_adwokat($id)
                                    ORDER BY total DESC LIMIT 1;";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $wyn = pg_fetch_all($val);

                        $najcz = $wyn[0]['klas'].": ".$wyn[0]['total'];
 

                        echo "<tr>";
                        echo "<td>Ilość spraw:</td><td>".$ilosc_spraw."</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Sprawy wygrane:</td><td>".$wygrane."%</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Sprawy przegrane:</td><td>".$przegrane."%</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo " <td>Sprawy w toku:</td><td>".$wtoku."%</td>";
                        echo "</tr>";                        
                        echo "<tr>";
                        echo " <td>Najczęstsze wyroki:</td><td>".$najcz."</td>";
                        echo "</tr>";                       
                        echo "<tr>";
                        echo " <td>Najrzadsze wyroki:</td><td>".$najrz."</td>";
                        echo "</tr>";
                        
                    ?>

                </table>
            </div>
        
        </div>
        <div style="clear:both;"></div>
        
    </body>

</html>
