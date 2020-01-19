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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Prokuratura Okręgowa</title>
    <link rel="stylesheet" href="../css/style1.css">


    <script type="text/javascript">
        function show(whichID){
          document.getElementById('tab1').style.display='none';
          document.getElementById('tab2').style.display='none';
          document.getElementById('tab3').style.display='none';
          document.getElementById('tab4').style.display='none';
          document.getElementById('tab5').style.display='none';
          document.getElementById('tab6').style.display='none';
          document.getElementById('tab7').style.display='none';
          document.getElementById('tab8').style.display='none';
          document.getElementById('tab9').style.display='none';
          document.getElementById('tab10').style.display='none';
          document.getElementById(whichID).style.display='block'
        }
    </script>
</head>
<body onload="show('temp')">
    <div id="dane"> 
    <a href="start.php">Wróć na panel prokuratora</a>
        <?php echo '<h3>Baza danych!</h3>'; ?>
    </div>
    <div id="menu">
        <input class="button" type="button" onclick="show('tab1')" value="Wszyscy prokuratorzy" ><br />
        <input class="button" type="button" onclick="show('tab2')" value="Wszyscy adwokaci" ><br />
        <input class="button" type="button" onclick="show('tab10')" value="Wszyscy oskarżeni" ><br />
        <input class="button" type="button" onclick="show('tab3')" value="Lista przestępstw" ><br />
        <input class="button" type="button" onclick="show('tab4')" value="Lista dowodów" ><br />
        <input class="button" type="button" onclick="show('tab5')" value="Lista wyroków" ><br />
        <input class="button" type="button" onclick="show('tab6')" value="Lista więzień" ><br />
        <input class="button" type="button" onclick="show('tab7')" value="Lista poszkodowanych" ><br />
        <input class="button" type="button" onclick="show('tab8')" value="Lista świadków" ><br />
        <input class="button" type="button" onclick="show('tab9')" value="Miejsca przestępstw" ><br />
    </div>

    <div id="ekran">
        <div id="tab1">
            <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Miejsce pracy</th>
                    <th>Numer licencji</th>
                    <th>Ilość prowadzonych aktualnie spraw</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $quest = "SELECT * FROM kartoteka.wszyscy_prokuratorzy";
                $val = pg_query($db, $quest);
                if(!$val) 
                    $_SESSION['error'] = "Błąd serwera! ".$quest;
                else 
                    $prokurator_tab = pg_fetch_all($val);
                
                foreach($prokurator_tab as $element) {
                    $tempID = $element['id'];
                    $quest = "SELECT COUNT(id) AS wynik FROM kartoteka.moje_sprawy_prokurator ($tempID)";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $wyn_tab = pg_fetch_all($val);
                    
                    $ilosc_spraw = $wyn_tab[0]['wynik'];
                
                    echo "<tr>";
                    echo "<td>".$element['id']."</td>";
                    echo "<td>".$element['imie']."</td>";
                    echo "<td>".$element['nazwisko']."</td>";
                    echo "<td>".$element['miejsce_pracy']."</td>";
                    echo "<td>".$element['numer_licencji']."</td>";
                    echo "<td>".$ilosc_spraw."</td>";
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
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Nazwa kancelarii</th>
                    <th>Numer licencji</th>
                    <th>Klienci</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $quest = "SELECT * FROM kartoteka.wszyscy_adwokaci";
                $val = pg_query($db, $quest);
                if(!$val) 
                    $_SESSION['error'] = "Błąd serwera! ".$quest;
                else 
                    $adwokat_tab = pg_fetch_all($val);
                
                foreach($adwokat_tab as $element) {
                    $tempID = $element['id'];
                    $quest = "SELECT * FROM kartoteka.oskarzony o
                                       WHERE o.adwokat_id = $tempID;";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $oskarzony_tab = pg_fetch_all($val);
                
                    echo "<tr>";
                    echo "<td>".$element['id']."</td>";
                    echo "<td>".$element['imie']."</td>";
                    echo "<td>".$element['nazwisko']."</td>";
                    echo "<td>".$element['nazwa_kancelarii']."</td>";
                    echo "<td>".$element['numer_licencji']."</td>";
                    echo "<td>";
                    foreach($oskarzony_tab as $osk)
                       echo $osk['imie']." ".$osk['nazwisko']."<br/>";
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
                    <th>ID</th>
                    <th>Zadarzenie</th>
                    <th>Motyw</th>
                    <th>Data</th>
                    <th>Miejsce</th>
                    <th>Poszkodowany</th>
                    <th>Świadek</th>
                    <th>Narzędzie</th>
                    <th>Oskarżony</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $quest = "SELECT * FROM kartoteka.przestepstwo";
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

                    //poszkodowany
                    $quest = "SELECT * FROM kartoteka.poszkodowany p
                                        WHERE p.przestepstwo_id = $tempID;";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $poszkodowany_tab = pg_fetch_all($val);

                    //swiadek
                    $quest = "SELECT * FROM kartoteka.swiadek s
                                        WHERE s.przestepstwo_id = $tempID;";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $swiadek_tab = pg_fetch_all($val);

                    //narzedzie
                    $quest = "SELECT * FROM kartoteka.narzedzie p
                                        WHERE p.przestepstwo_id = $tempID;";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $narzedzie_tab = pg_fetch_all($val);


                    //oskarzony
                    $quest = "SELECT * FROM kartoteka.oskarzony o
                                       WHERE o.id IN (SELECT oskarzony_id 
                                                      FROM kartoteka.oskarzony_przestepstwo op
                                                      WHERE op.przestepstwo_id = $tempID);";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $oskarzony_tab = pg_fetch_all($val);
                
                    echo "<tr>";
                    echo "<td>".$element['id']."</td>";
                    echo "<td>".$element['typ']."</td>";
                    echo "<td>".$element['motyw']."</td>";
                    echo "<td>".$element['data_przestepstwa']."</td>";
                    echo "<td>".$miejsce_info."</td>";
                    echo "<td>";
                    foreach($poszkodowany_tab as $el)
                       echo $el['imie']." ".$el['nazwisko']."<br/>";
                    echo "</td>";
                    echo "<td>";
                    foreach($swiadek_tab as $el)
                       echo $el['imie']." ".$el['nazwisko']."<br/>";
                    echo "</td>";
                    echo "<td>";
                    foreach($narzedzie_tab as $el)
                       echo $el['numer_dowodu'].", ".$el['narzedzie']."<br/>";
                    echo "</td>";
                    echo "<td>";
                    foreach($oskarzony_tab as $el)
                       echo $el['imie']." ".$el['nazwisko']."<br/>";
                    echo "</td>";
                    echo "</tr>";
                }
            ?>
            </tbody>
            </table>
        </div>
        <div id="tab4">
            <table>
            <thead>
                <tr> 
                    <th>Numer dowodu</th>                   
                    <th>Dowód</th>
                    <th>Miejsce przechowywania</th>
                    <th>Sprawa</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $quest = "SELECT * FROM kartoteka.narzedzia_baza";
                $val = pg_query($db, $quest);
                if(!$val) 
                    $_SESSION['error'] = "Błąd serwera! ".$quest;
                else 
                    $tab = pg_fetch_all($val);
                
                foreach($tab as $element) {
                    echo "<tr>";
                    echo "<td>".$element['numer_dowodu']."</td>";
                    echo "<td>".$element['narzedzie']."</td>";
                    echo "<td>".$element['miejsce_przechowania']."</td>";
                    echo "<td>".$element['typ'].", ".$element['data_przestepstwa']."</td>";
                    echo "</tr>";
                }
            ?>
            </tbody>
            </table>
        </div>
        <div id="tab5">
            <table>
            <thead>
                <tr> 
                    <th>Oskarżony</th>                   
                    <th>Sprawa</th>                   
                    <th>Status winy</th>                   
                    <th>Klasyfikacja wyroku</th>
                    <th>Więzienie</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $quest = "SELECT * FROM kartoteka.wyrok";
                $val = pg_query($db, $quest);
                if(!$val) 
                    $_SESSION['error'] = "Błąd serwera! ".$quest;
                else 
                    $tab = pg_fetch_all($val);
                
                foreach($tab as $element) {
                    $tempID = $element['id'];
                    $tempIDw = $element['wiezienie_id'];

                    //Wybierz oskarzonego
                    $quest = "SELECT * FROM kartoteka.oskarzony o
                              WHERE o.id = (SELECT oskarzony_id 
                                            FROM kartoteka.oskarzony_przestepstwo op 
                                            WHERE wyrok_id = $tempID);";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $osk = pg_fetch_all($val);
                    $osk_info = $osk[0]['imie']." ".$osk[0]['nazwisko'];

                    //Wybierz przestepstwo
                    $quest = "SELECT * FROM kartoteka.przestepstwo o
                              WHERE o.id = (SELECT przestepstwo_id 
                                            FROM kartoteka.oskarzony_przestepstwo op 
                                            WHERE wyrok_id = $tempID);";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $prze = pg_fetch_all($val);
                    $prze_info = $prze[0]['typ']." - ".$prze[0]['data_przestepstwa'];

                    //wybierz wiezienie
                    $quest = "SELECT * FROM kartoteka.wiezienie w
                              WHERE w.id = $tempIDw";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $wie = pg_fetch_all($val);
                    
                    $wie_info = $wie[0]['miasto'].", ".$wie[0]['kraj']." - ".$wie[0]['typ'];
                    if($element['status_winy'] != 'winny') $wie_info = 'Brak';
                    
                    if($prze[0]['typ']){
                    echo "<tr>";
                    echo "<td>".$osk_info."</td>";
                    echo "<td>".$prze_info."</td>";
                    echo "<td>".$element['status_winy']."</td>";
                    echo "<td>".$element['klasyfikacja']."</td>";
                    echo "<td>".$wie_info."</td>";
                    echo "</tr>";
                    }
                }
            ?>
            </tbody>
            </table>
        </div>
        <div id="tab6">
            <table>
            <thead>
                <tr> 
                    <th>Miasto</th>                   
                    <th>Kraj</th>
                    <th>Typ</th>
                    <th>Ilość osadzonych (podział na długość wyroku)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $quest = "SELECT * FROM kartoteka.wiezienie";
                $val = pg_query($db, $quest);
                if(!$val) 
                    $_SESSION['error'] = "Błąd serwera! ".$quest;
                else 
                    $tab = pg_fetch_all($val);
                
                foreach($tab as $element) {
                    $idw = $element['id'];
                    if($idw != 0){
                        //ilosc osadzonych wzgledem klasyfikacji
                        $quest = "SELECT * FROM kartoteka.ilosc_osadzonych_wzgl_klasyfikacji($idw)";
                        $val = pg_query($db, $quest);
                        if(!$val) 
                            $_SESSION['error'] = "Błąd serwera! ".$quest;
                        else 
                            $klasy = pg_fetch_all($val);
                        
    

                        echo "<tr>";
                        echo "<td>".$element['miasto']."</td>";
                        echo "<td>".$element['kraj']."</td>";
                        echo "<td>".$element['typ']."</td>";
                        echo "<td>";
                        //TU WEDLUG KASYFIKACJI
                        foreach($klasy as $wyroki){
                            if(($wyroki['klas'] != 'Brak') && ($wyroki['klas'] != 'Kara pieniężna')  && ($wyroki['klas'] != 'Wyrok w zawieszeniu') )
                            echo $wyroki['klas']." : ".$wyroki['total']."<br/>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                }
            ?>
            </tbody>
            </table>
        </div>
        <div id="tab7">
            <table>
            <thead>
                <tr> 
                    <th>Imię</th>                   
                    <th>Nazwisko</th>
                    <th>Straty</th>
                    <th>Przestępstwo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $quest = "SELECT * FROM kartoteka.poszkodowany";
                $val = pg_query($db, $quest);
                if(!$val) 
                    $_SESSION['error'] = "Błąd serwera! ".$quest;
                else 
                    $tab = pg_fetch_all($val);
                
                foreach($tab as $element) {
                    $idprz = $element['przestepstwo_id'];
                    //przestepstwo
                    $quest = "SELECT * FROM kartoteka.przestepstwo p WHERE p.id = $idprz";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $prze = pg_fetch_all($val);

                    echo "<tr>";
                    echo "<td>".$element['imie']."</td>";
                    echo "<td>".$element['nazwisko']."</td>";
                    echo "<td>".$element['straty']."</td>";
                    echo "<td>".$prze[0]['typ'].", ".$prze[0]['data_przestepstwa']."</td>";
                    echo "</tr>";
                    
                }
                ?>
            </tbody>
            </table>
        </div>
        <div id="tab8">
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
                $quest = "SELECT * FROM kartoteka.swiadek";
                $val = pg_query($db, $quest);
                if(!$val) 
                    $_SESSION['error'] = "Błąd serwera! ".$quest;
                else 
                    $tab = pg_fetch_all($val);
                
                foreach($tab as $element) {
                    $idprz = $element['przestepstwo_id'];
                    //przestepstwo
                    $quest = "SELECT * FROM kartoteka.przestepstwo p WHERE p.id = $idprz";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $prze = pg_fetch_all($val);

                    echo "<tr>";
                    echo "<td>".$element['imie']."</td>";
                    echo "<td>".$element['nazwisko']."</td>";
                    echo "<td>".$prze[0]['typ'].", ".$prze[0]['data_przestepstwa']."</td>";
                    echo "</tr>";
                    
                }
                ?>
            </tbody>
            </table>        
        </div>
        <div id="tab9">
            <table>
            <thead>
                <tr> 
                    <th>Kraj</th>                   
                    <th>Miasta</th>
                    <th>Procent wszystkich przestępstw</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $quest = "SELECT * FROM kartoteka.miejsce_przestepstwa";
                $val = pg_query($db, $quest);
                if(!$val) 
                    $_SESSION['error'] = "Błąd serwera! ".$quest;
                else 
                    $tab = pg_fetch_all($val);

                //tworzenie tabeli bez powtarzania krajów
                $tab_ok = array();
                foreach($tab as $el){
                    if(!in_array($el['kraj'],$tab_ok))
                         array_push($tab_ok, $el['kraj']);
                }
                
                foreach($tab_ok as $element) {
                    //wszystkie miasta
                    $kr = $element;
                    $quest = "SELECT miasto FROM kartoteka.miejsce_przestepstwa WHERE kraj = '$kr';";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $miasta = pg_fetch_all($val);

                    //statystyki dla kraju
                    $quest = "SELECT COUNT(*) as total FROM kartoteka.przestepstwo;";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $all = pg_fetch_all($val);
                    $wszystko = $all[0]['total'];

                    $quest = "SELECT COUNT(*) as total FROM kartoteka.przestepstwo WHERE miejsce_id IN (SELECT id FROM kartoteka.miejsce_przestepstwa where kraj = '$kr');";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $all = pg_fetch_all($val);
                    $czesc = $all[0]['total'];

                    $procent = ($czesc/$wszystko) * 100;
                    if($czesc){
                    echo "<tr>";
                    echo "<td>".$element."</td>";
                    echo "<td>";
                    foreach($miasta as $miasto)
                        echo $miasto['miasto']."<br/>";
                    echo"</td>";  
                    echo "<td>".$procent."% </td>";
                    echo "</tr>";
                    }
                }
                ?>
            </tbody>
            </table>
        </div>
        <div id="tab10">
            <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Adwokat</th>
                    <th>Ilość przestępstw</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $quest = "SELECT * FROM kartoteka.wszyscy_oskarzeni";
                $val = pg_query($db, $quest);
                if(!$val) 
                    $_SESSION['error'] = "Błąd serwera! ".$quest;
                else 
                    $tab = pg_fetch_all($val);
                
                foreach($tab as $element) {
                    $tempID = $element['adwokat_id'];
                    $quest = "SELECT * FROM kartoteka.adwokat WHERE id = $tempID;";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $tab = pg_fetch_all($val);
                    
                    $adwokat_info = $tab[0]['imie']." ".$tab[0]['nazwisko'];

                    //ilosc przestepstw
                    $tempID = $element['id'];
                    $quest = "SELECT COUNT(*) as total FROM kartoteka.wszystkie_moje_przestepstwa_oskarzony($tempID);";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $tab = pg_fetch_all($val);
                    
                    $ilosc_spraw = $tab[0]['total'];


                    echo "<tr>";
                    echo "<td>".$element['id']."</td>";
                    echo "<td>".$element['imie']."</td>";
                    echo "<td>".$element['nazwisko']."</td>";
                    echo "<td>".$adwokat_info."</td>";
                    echo "<td>".$ilosc_spraw."</td>";
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
