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
    $quest = "SELECT * FROM kartoteka.moje_dane_prokurator ('$email' , '$haslo');";
    $me = pg_query($db, $quest);

    if(!$me) 
        $_SESSION['error'] = "Błąd serwera! ".$quest;
    else {
        $tab = pg_fetch_all($me);
            if($tab[0]['id'] === null) {
               $_SESSION['error'] = "Prokurator o tym e-mail nie istnieje!";
               exit();
            }
            else{
                $id = $_SESSION['id'] = $tab[0]['id'];
                $_SESSION['imie'] = $tab[0]['imie'];
                $_SESSION['nazwisko'] = $tab[0]['nazwisko'];
                $_SESSION['miejsce_pracy'] = $tab[0]['miejsce_pracy'];
                $_SESSION['numer_licencji'] = $tab[0]['numer_licencji'];
               }
       }


    $quest = "SELECT * FROM kartoteka.moi_oskarzeni_prokurator ($id) ORDER BY id;";
    $oskarzeni = pg_query($db, $quest);

    if(!$oskarzeni) 
        $_SESSION['error'] = "Błąd serwera! ".$quest;
    else 
        $oskarzeni_tab = pg_fetch_all($oskarzeni);
    
    $quest = "SELECT * FROM kartoteka.wszyscy_oskarzeni  ORDER BY id;";
    $oskarzeni = pg_query($db, $quest);
    
        if(!$oskarzeni) 
            $_SESSION['error'] = "Błąd serwera! ".$quest;
        else 
            $all_oskarzeni_tab = pg_fetch_all($oskarzeni);
        
    $quest = "SELECT * FROM kartoteka.wszyscy_adwokaci  ORDER BY id;";
    $adwokaci = pg_query($db, $quest);
    if(!$adwokaci) 
        $_SESSION['error'] = "Błąd serwera! ".$quest;

    else 
        $adwokaci_tab = pg_fetch_all($adwokaci);
    


    $quest = "SELECT * FROM kartoteka.wszystkie_wiezienia  ORDER BY id;";
    $wiezienie = pg_query($db, $quest);
    if(!$wiezienie) {
        $_SESSION['error'] ="Błąd serwera! ".$quest;
    } 
    else {
        $wiezienie_tab = pg_fetch_all($wiezienie);
    }

    $quest = "  SELECT * FROM kartoteka.moje_sprawy_prokurator ($id)  ORDER BY id;";
    $przestepstwa = pg_query($db, $quest);
    if(!$przestepstwa) {
        $_SESSION['error'] ="Błąd serwera! ".$quest;
    } 
    else {
        $przestepstwa_tab = pg_fetch_all($przestepstwa);
    }

    $quest = "  SELECT * FROM kartoteka.przestepstwo  ORDER BY id;";
    $przestepstwa = pg_query($db, $quest);
    if(!$przestepstwa) {
        $_SESSION['error'] ="Błąd serwera! ".$quest;
    } 
    else {
        $przestepstwa_tab_all = pg_fetch_all($przestepstwa);
    }

    $quest = "  SELECT * FROM kartoteka.narzedzie WHERE przestepstwo_id IN (SELECT id FROM kartoteka.moje_sprawy_prokurator ($id))  ORDER BY numer_dowodu;";
    $narz = pg_query($db, $quest);
    if(!$narz) {
        $_SESSION['error'] ="Błąd serwera! ".$quest;
    } 
    else {
        $moje_narzedzia_tab = pg_fetch_all($narz);
    }

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Prokuratura Okręgowa</title>
    <link rel="stylesheet" href="../css/style1.css"> 
    <style>
        .error{
            color: red;
            font-weight: bold;
        }
    </style>

    <script type="text/javascript">
        var firsttime = true;
        window.onload = function(){
            if(firsttime){ 
                show('temp');
                firsttime = false;
                }
        }

        function show(whichID){
            document.getElementById('formularzP').style.display='none';
            document.getElementById('formularzO').style.display='none';
            document.getElementById('tabelaO').style.display='none';
            document.getElementById('tabelaP').style.display='none';
            document.getElementById('tabelaA').style.display='none';
            document.getElementById("formularzSwiadek").style.display='none';
            document.getElementById("formularzNarzedzie").style.display='none';
            document.getElementById("formularzPoszkodowany").style.display='none';
            document.getElementById("usunButtons").style.display='none';
            document.getElementById("modyfikujButtons").style.display='none';
            document.getElementById("formularzUsunOskarzonego").style.display='none';
            document.getElementById("formularzUsunPrzestepstwo").style.display='none';
            document.getElementById("formularzUsunNarzedzie").style.display='none';
            document.getElementById("formularzModyfikujOskarzonego").style.display='none';
            document.getElementById("formularzModyfikujPrzestepstwo").style.display='none';
            document.getElementById("formularzModyfikujWyrok").style.display='none';
            if(whichID == 'formularzUsunOskarzonego' || whichID =='formularzUsunPrzestepstwo' || whichID =="formularzUsunNarzedzie")
                document.getElementById("usunButtons").style.display='block';
            if(whichID == 'formularzModyfikujOskarzonego' || whichID =='formularzModyfikujPrzestepstwo'|| whichID =='formularzModyfikujWyrok')
                document.getElementById("modyfikujButtons").style.display='block';
            document.getElementById(whichID).style.display='block'
        }
    </script>
</head>
<body>
    <div id="dane"> 
    <a href="../logowanie/wyloguj.php">Wyloguj</a>
        <?php echo '<h3>Prokurator: </h3><h4>';
        echo $_SESSION['imie']; 
        echo ' '.$_SESSION['nazwisko']; 
        echo '<br/>nr licencji: '.$_SESSION['numer_licencji']."</h4><br/>";
        ?>

    </div>
    <div id="menu">
    <input class="button" type="button" onclick="show('formularzP')" value="Dodaj nowe przestępstwo" ><br />
    <input class="button" type="button" onclick="show('formularzO')" value="Dodaj nowego podejrzanego" ><br />
    <input class="button" type="button" onclick="show('tabelaO')" value="Wyświetl moich oskarżonych" ><br />
    <input class="button" type="button" onclick="show('tabelaP')" value="Wyświetl prowadzone sprawy" ><br />
    <input class="button" type="button" onclick="show('tabelaA')" value="Lista adwokatów" ><br />
    <input class="button" type="button" onclick="show('usunButtons')" value="Usuń" ><br />
    <input class="button" type="button" onclick="show('modyfikujButtons')" value="Modyfikuj" ><br />
    <div id="space"></div>
    <a class="button" href="baza.php">Baza przestępstw</a>
    </div>

    <div id="ekran">
        <div id="formularzP">
            <form action="dodajPrzestepstwo.php" method="POST">
            Zdarzenie:  <?php echo "<div class='error'>";if(isset($_SESSION['typERROR'])) {echo $_SESSION['typERROR']; unset($_SESSION['typERROR']);} echo "</div>"; ?> 
            <input type="text" name="typ" value="<?php if(isset($_SESSION['typ'])) { echo $_SESSION['typ']; unset($_SESSION['typ']); } else { echo ''; } ?>"><br /> 
           
            Motyw: <?php echo "<div class='error'>"; if(isset($_SESSION['motywERROR'])) {echo $_SESSION['motywERROR']; unset($_SESSION['motywERROR']);} echo "</div>"; ?> 
            <input type="text" name="motyw" value="<?php if(isset($_SESSION['motyw'])) { echo $_SESSION['motyw']; unset($_SESSION['motyw']);} else {echo ''; } ?>"> <br />  
       
            Data: <input type="date" name="data_przestepstwa" required> <br />
        
            Miasto:  <?php echo "<div class='error'>"; if(isset($_SESSION['miastoERROR'])) {echo $_SESSION['miastoERROR']; unset($_SESSION['miastoERROR']);} echo "</div>"; ?> 
            <input type="text" name="miasto" value="<?php if(isset($_SESSION['miasto'])) { echo $_SESSION['miasto']; unset($_SESSION['miasto']); } else { echo ''; } ?>"><br /> 
           
        
            Kraj:  <?php echo "<div class='error'>"; if(isset($_SESSION['krajERROR'])) {echo $_SESSION['krajERROR']; unset($_SESSION['krajERROR']);} echo "</div>"; ?> 
            <input type="text" name="kraj" value="<?php if(isset($_SESSION['kraj'])) { echo $_SESSION['kraj']; unset($_SESSION['kraj']);} else {echo ''; } ?>"><br />
           
        
            Dowód: <?php echo "<div class='error'>"; if(isset($_SESSION['narzedzieERROR'])) {echo $_SESSION['narzedzieERROR']; unset($_SESSION['narzedzieERROR']);} echo "</div>"; ?>
            <input type="text" name="narzedzie" value="<?php if(isset($_SESSION['narzedzie'])) { echo $_SESSION['narzedzie']; unset($_SESSION['narzedzie']); } else { echo ''; } ?>">
             <br /> 
        
            Numer dowodu:  <?php echo "<div class='error'>"; if(isset($_SESSION['numer_dowoduERROR'])) {echo $_SESSION['numer_dowoduERROR']; unset($_SESSION['numer_dowoduERROR']);} echo "</div>"; ?>
            <input type="text" name="numer_dowodu" value="<?php if(isset($_SESSION['numer_dowodu'])) { echo $_SESSION['numer_dowodu']; unset($_SESSION['numer_dowodu']);} else {echo ''; } ?>">
            <br />
        
            Miejsce przechowywanie dowodu: <?php echo "<div class='error'>"; if(isset($_SESSION['miejsce_przechowaniaERROR'])) {echo $_SESSION['miejsce_przechowaniaERROR']; unset($_SESSION['miejsce_przechowaniaERROR']);} echo "</div>"; ?>
            <input type="text" name="miejsce_przechowania" value="<?php if(isset($_SESSION['miejsce_przechowania'])) { echo $_SESSION['miejsce_przechowania']; unset($_SESSION['miejsce_przechowywania']); } else { echo ''; } ?>">
             <br /> 
            <input class="buttonS" type="submit" value="Dodaj">
            </form>
            <input class="button1" type="button" onclick="show('formularzSwiadek')" value="Dodaj świadka" ><br />
            <input class="button1" type="button" onclick="show('formularzNarzedzie')" value="Dodaj nowe narzędzie zbrodni" ><br />
            <input class="button1" type="button" onclick="show('formularzPoszkodowany')" value="Dodaj poszkodowanego" ><br />
        </div>
        <div id="formularzSwiadek">
            <form form action="dodajSwiadek.php" method="POST">
            Wybierz sprawę:
            <select name="sprawa_id">
            <?php 
                foreach($przestepstwa_tab as $przestepstwa) {
                    $numer_typ = $przestepstwa['typ'].", ".$przestepstwa['data_przestepstwa'];
                    echo '<option value="'.$przestepstwa['id'].'">'.$numer_typ.'</option>';
                } 
            ?>
            </select><br />
            Imię świadka: <?php echo "<div class='error'>"; if(isset($_SESSION['imieSERROR'])) {echo $_SESSION['imieSERROR']; unset($_SESSION['imieSERROR']);} echo "</div>"; ?>
            <input type="text" name="imieS" value="<?php if(isset($_SESSION['imieS'])) { echo $_SESSION['imieS']; unset($_SESSION['imieS']);} else {echo ''; } ?>">
             <br />
            Nazwisko świadka:<?php echo "<div class='error'>"; if(isset($_SESSION['nazwiskoSERROR'])) {echo $_SESSION['nazwiskoSERROR']; unset($_SESSION['nazwiskoSERROR']);} echo "</div>"; ?>
             <input type="text" name="nazwiskoS" value="<?php if(isset($_SESSION['nazwiskoS'])) { echo $_SESSION['nazwiskoS']; unset($_SESSION['nazwiskoS']);} else {echo ''; } ?>">
             <br />
            <input class="buttonS" type="submit" value="Dodaj"> 
            </form>
        </div>
        <div id="formularzNarzedzie">
            <form form action="dodajNarzedzie.php" method="POST">
            Wybierz sprawę:
            <select name="sprawa_id">
            <?php 
                foreach($przestepstwa_tab as $przestepstwa) {
                    $numer_typ = $przestepstwa['typ'].", ".$przestepstwa['data_przestepstwa'];
                    echo '<option value="'.$przestepstwa['id'].'">'.$numer_typ.'</option>';
                } 
            ?>
            </select><br />
            Dowód: <?php echo "<div class='error'>"; if(isset($_SESSION['narzedzieNERROR'])) {echo $_SESSION['narzedzieNERROR']; unset($_SESSION['narzedzieNERROR']);} echo "</div>";  ?>
            <input type="text" name="narzedzieN" value="<?php if(isset($_SESSION['narzedzieN'])) { echo $_SESSION['narzedzieN']; unset($_SESSION['narzedzieN']); } else { echo ''; } ?>">
             <br /> 
        
            Numer dowodu: <?php echo "<div class='error'>"; if(isset($_SESSION['numer_dowoduNERROR'])) {echo $_SESSION['numer_dowoduNERROR']; unset($_SESSION['numer_dowoduNERROR']);} echo "</div>"; ?>
            <input type="text" pattern="[0-9]{5}" name="numer_dowoduN" value="<?php if(isset($_SESSION['numer_dowoduN'])) { echo $_SESSION['numer_dowoduN']; unset($_SESSION['numer_dowoduN']);} else {echo ''; } ?>">
             <br />
        
            Miejsce przechowywanie dowodu:<?php echo "<div class='error'>"; if(isset($_SESSION['miejsce_przechowaniaNERROR'])) {echo $_SESSION['miejsce_przechowaniaNERROR']; unset($_SESSION['miejsce_przechowaniaNERROR']);} echo "</div>"; ?>
             <input type="text" name="miejsce_przechowaniaN" value="<?php if(isset($_SESSION['miejsce_przechowaniaN'])) { echo $_SESSION['miejsce_przechowaniaN']; unset($_SESSION['miejsce_przechowaniaN']); } else { echo ''; } ?>">
             <br /> 
            <input class="buttonS" type="submit" value="Dodaj"> 
            </form>
        </div>
        <div id="formularzPoszkodowany">
            <form form action="dodajPoszkodowanego.php" method="POST">
            Wybierz sprawę:
            <select name="sprawa_id">
            <?php 
                foreach($przestepstwa_tab as $przestepstwa) {
                    $numer_typ = $przestepstwa['typ'].", ".$przestepstwa['data_przestepstwa'];
                    echo '<option value="'.$przestepstwa['id'].'">'.$numer_typ.'</option>';
                } 
            ?>
            </select><br />
            Imię poszkodowanego:<?php echo "<div class='error'>"; if(isset($_SESSION['imiePERROR'])) {echo $_SESSION['imiePERROR']; unset($_SESSION['imiePERROR']);} echo "</div>";  ?>
             <input type="text" name="imieP" value="<?php if(isset($_SESSION['imieP'])) { echo $_SESSION['imieP']; unset($_SESSION['imieP']);} else {echo ''; } ?>">
             <br />
            Nazwisko poszkodowanego: <?php echo "<div class='error'>"; if(isset($_SESSION['nazwiskoPERROR'])) {echo $_SESSION['nazwiskoPERROR']; unset($_SESSION['nazwiskoPERROR']);} echo "</div>"; ?>
            <input type="text" name="nazwiskoP" value="<?php if(isset($_SESSION['nazwiskoP'])) { echo $_SESSION['nazwiskoP']; unset($_SESSION['nazwiskoP']);} else {echo ''; } ?>">
             <br />
            Rodzaj strat: 
            <select name="straty">
                <option value="1">Straty materialne</option>
                <option value="2">Lekki uszczerbek na zdrowiu</option>
                <option value="3">Uszczerbek na zdrowiu</option>
                <option value="4">Znaczący uszczerbek na zdrowiu</option>
                <option value="5">Śmierć</option>
            </select><br/>
            <input class="buttonS" type="submit" value="Dodaj"> 
            </form>
        </div>
        <div id="formularzO">
            <form action="dodajOskarzonego.php" method="POST">
            Imię: <?php echo "<div class='error'>"; if(isset($_SESSION['imieOERROR'])) {echo $_SESSION['imieOERROR']; unset($_SESSION['imieOERROR']);} echo "</div>";  ?>
            <input type="text" name="imieO" value="<?php if(isset($_SESSION['imieO'])) { echo $_SESSION['imieO']; unset($_SESSION['imieO']); } else { echo ''; } ?>">
             <br /> 
            Nazwisko: <?php echo "<div class='error'>"; if(isset($_SESSION['nazwiskoOERROR'])) {echo $_SESSION['nazwiskoOERROR']; unset($_SESSION['nazwiskoOERROR']);} echo "</div>";  ?>
            <input type="text" name="nazwiskoO" value="<?php if(isset($_SESSION['nazwiskoO'])) { echo $_SESSION['nazwiskoO']; unset($_SESSION['nazwiskoO']);} else {echo ''; } ?>">
             <br />
            <!-- ID aktualnego prokuratora -->
                <select name="id" style="display:none;">    
                <?php echo '<option value="'.$id.'"> </option>';?>
                </select>
            Adwokat: 
            <select name="adwokat_id">
            <?php 
                foreach($adwokaci_tab as $adwokat) {
                    $imie_nazwisko = $adwokat['imie']." ".$adwokat['nazwisko'];
                    echo '<option value="'.$adwokat['id'].'">'.$imie_nazwisko.'</option>';
                } 
            ?>
            </select><br />
            Wybierz przestępstwo:
            <select name="przestepstwo_id_FO">
            <?php 
                foreach($przestepstwa_tab_all as $przestepstwa) {
                    $numer_typ = $przestepstwa['typ'].", ".$przestepstwa['data_przestepstwa'];
                    echo '<option value="'.$przestepstwa['id'].'">'.$numer_typ.'</option>';
                } 
            ?>
            </select><br />
            Status winy: 
            <select name="status_winy">
                <option value="1">Winny</option>
                <option value="2">Niewinny</option>
                <option value="3">Postępowanie w toku</option>
            </select><br />
            Klasyfikacja wyroku: <?php echo "<div class='error'>"; if(isset($_SESSION['kalsyfikacjaERROR'])) {echo $_SESSION['kalsyfikacjaERROR']; unset($_SESSION['kalsyfikacjaERROR']);} echo "</div>"; ?> 
            <select name="klasyfikacja">
                <option value="1">Brak</option>
                <option value="2">Kara pieniężna</option>
                <option value="3">Wyrok w zawieszeniu</option>
                <option value="4">Kara więzienna lekka - do 1 roku</option>
                <option value="5">Kara więzienna średnia - od 1 roku - 10 lat</option>
                <option value="6">Kara więzienna wysoka - od 10 lat</option>
                <option value="7">Kara śmierci</option>
            </select><br />
            Więzienie: <?php echo "<div class='error'>"; if(isset($_SESSION['wiezienieERROR'])) {echo $_SESSION['wiezienieERROR']; unset($_SESSION['wiezienieERROR']);} echo "</div>"; ?>
            <select name="wiezienie_id"> 
                <option value="0">Brak</option>
            <?php 
                foreach($wiezienie_tab as $wiezienie) {
                    if($wiezienie['id']!=0){
                    $miasto_kraj_typ = $wiezienie['miasto'].", ".$wiezienie['kraj']." - ".$wiezienie['typ'];
                    echo '<option value="'.$wiezienie['id'].'">'.$miasto_kraj_typ.'</option>';
                }
                } 
            ?>
            </select> <br />
            <input class="buttonS" type="submit" value="Dodaj">
            </form></br>
            <p>Brak przestępstwa w bazie?</p>
            <input class="button1" type="button" onclick="show('formularzP')" value="Dodaj przestępstwo" ><br />
        </div>
        <div id="tabelaO">
            <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Adwokat</th>
                    <th>ID przestępstw</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($oskarzeni_tab as $oskarzeni) {
                    $tempID = $oskarzeni['adwokat_id'];
                    $quest = "SELECT * FROM kartoteka.adwokat_oskarzonego ($tempID)";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $imie_nazwisko_adwokata_temp = pg_fetch_all($val);
                    
                    $imie_nazwisko_adwokata = $imie_nazwisko_adwokata_temp[0]['imie']." ".$imie_nazwisko_adwokata_temp[0]['nazwisko'];

                    $tempOID =  $oskarzeni['id'];
                    $quest = "  SELECT * FROM kartoteka.wszystkie_moje_przestepstwa_oskarzony ($tempOID)";
                    $val = pg_query($db, $quest);
                    if(!$val) 
                        $_SESSION['error'] = "Błąd serwera! ".$quest;
                    else 
                        $indywidualne_przestepstwa_tab = pg_fetch_all($val);
                    
                    echo "<tr>";
                    echo "<td>".$oskarzeni['id']."</td>";
                    echo "<td>".$oskarzeni['imie']."</td>";
                    echo "<td>".$oskarzeni['nazwisko']."</td>";
                    echo "<td>".$imie_nazwisko_adwokata."</td>";
                    echo "<td>";
                    foreach($indywidualne_przestepstwa_tab as $przestepstwa)
                        echo $przestepstwa['id']." ";
                    echo "</td>";
                    echo "</tr>";
                }
            ?>
            </tbody>
            </table>
        </div>
        <div id="tabelaP">
            <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Zdarzenie</th>
                    <th>Motyw</th>
                    <th>Data</th>
                    <th>Miejsce</th>
                    <th>Narzędzie</th>
                    <th>Świadek</th>
                    <th>Poszkodowany</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($przestepstwa_tab as $przestepstwa) {
                    // MIEJSCE PRZESTEPSTWA
                    $temp = $przestepstwa['miejsce_id'];
                    $quest = "SELECT * FROM kartoteka.miejsce_przestepstwa mp WHERE mp.id = $temp;";
                    $miejsce = pg_query($db, $quest);
                    if(!$miejsce) {
                        $_SESSION['error'] ="Błąd serwera! ".$quest;
                    } 
                    else {
                        $miejsce_tab = pg_fetch_all($miejsce);
                    }
                    $miasto_kraj = $miejsce_tab[0]['miasto'].", ".$miejsce_tab[0]['kraj'];    
                    
                    // Narzędzia
                    $temp = $przestepstwa['id'];
                    $quest = "SELECT * FROM kartoteka.narzedzie n WHERE n.przestepstwo_id = $temp;";
                    $val = pg_query($db, $quest);
                    if(!$val) {
                        $_SESSION['error'] ="Błąd serwera! ".$quest;
                    } 
                    else {
                        $narzedzia_tab = pg_fetch_all($val);
                    }

                    // Swiadkowie
                    $temp = $przestepstwa['id'];
                    $quest = "SELECT * FROM kartoteka.swiadek s WHERE s.przestepstwo_id = $temp;";
                    $val = pg_query($db, $quest);
                    if(!$val) {
                        $_SESSION['error'] ="Błąd serwera! ".$quest;
                    } 
                    else {
                        $swiadek_tab = pg_fetch_all($val);
                    }

                    // Poszkodowani
                    $temp = $przestepstwa['id'];
                    $quest = "SELECT * FROM kartoteka.poszkodowany p WHERE p.przestepstwo_id = $temp;";
                    $val = pg_query($db, $quest);
                    if(!$val) {
                        $_SESSION['error'] ="Błąd serwera! ".$quest;
                    } 
                    else {
                        $poszkodowany_tab = pg_fetch_all($val);
                    }


                    echo "<tr>";
                    echo "<td>".$przestepstwa['id']."</td>";
                    echo "<td>".$przestepstwa['typ']."</td>";
                    echo "<td>".$przestepstwa['motyw']."</td>";
                    echo "<td>".$przestepstwa['data_przestepstwa']."</td>";
                    echo "<td>".$miasto_kraj."</td>";
                    echo "<td>";
                    foreach($narzedzia_tab as $element)
                        echo $element['numer_dowodu']." ".$element['narzedzie']."<br/>";
                    echo "</td>";
                    echo "<td>";
                    foreach($swiadek_tab as $element)
                        echo $element['imie']." ".$element['nazwisko']."<br/>";
                    echo "</td>";
                    echo "<td>";
                    foreach($poszkodowany_tab as $element)
                        echo $element['imie']." ".$element['nazwisko']."<br/>";
                    echo "</td>";
                    echo "</tr>";
                }
            ?>
            </tbody>
            </table>
        </div>
        <div id="tabelaA">
            <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Nazwa kancelarii</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($adwokaci_tab as $element) {
                        echo "<tr>";
                        echo "<td>".$element['id']."</td>";
                        echo "<td>".$element['imie']."</td>";
                        echo "<td>".$element['nazwisko']."</td>";
                        echo "<td>".$element['nazwa_kancelarii']."</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
            </table>
        </div>
        <div id="usunButtons">
            <input class="button2" type="button" onclick="show('formularzUsunOskarzonego')" value="Usuń podejrzanego" >
            <input class="button2" type="button" onclick="show('formularzUsunPrzestepstwo')" value="Usuń przestepstwo" >
            <input class="button2" type="button" onclick="show('formularzUsunNarzedzie')" value="Usuń narzędzie" >
            <div style="clear:both;"></div>        
        </div>
        <div id="modyfikujButtons">
            <input class="button2" type="button" onclick="show('formularzModyfikujOskarzonego')" value="Modyfikuj podejrzanego" >
            <input class="button2" type="button" onclick="show('formularzModyfikujPrzestepstwo')" value="Modyfikuj przestępstwo" >
            <input class="button2" type="button" onclick="show('formularzModyfikujWyrok')" value="Modyfikuj wyrok" >
            <div style="clear:both;"></div>
        </div>
        <div id = "formularzUsunOskarzonego">
            <form action="usunOskarzonego.php" method="POST">
            Wybierz oskarżonego:
            <select name="oskarzony_id">
            <?php 
                  foreach($oskarzeni_tab as $element) {
                            $id_imie_nazwisko = $element['imie']." ".$element['nazwisko'];
                            echo '<option value="'.$element['id'].'">'.$id_imie_nazwisko.'</option>';
                        } 
                ?>
                </select> <br/>
                <input class="buttonS" type="submit" value="Usuń oskarżonego">
            </form>
        </div>
        <div id = "formularzUsunNarzedzie">
            <form action="usunNarzedzie.php" method="POST">
            Wybierz narzędzie:
            <select name="przestepstwo_id_usun">
            <?php 
                  foreach($moje_narzedzia_tab as $element) {
                            $id_typ_data = $element['numer_dowodu']." ".$element['narzedzie'];
                            echo '<option value="'.$element['id'].'">'.$id_typ_data.'</option>';
                        } 
                ?>
                </select><br />
                <input class="buttonS" type="submit" value="Usuń narzędzie">
            </form>
        </div>
        <div id = "formularzUsunPrzestepstwo">
            <form action="usunPrzestepstwo.php" method="POST">
            Wybierz przestepstwo:
            <select name="przestepstwo_id_usun">
            <?php 
                  foreach($przestepstwa_tab as $element) {
                            echo '<option value="'.$element['id'].'">'.$element['typ'].', '.$element['data_przestepstwa'].'</option>';
                        } 
                ?>
                </select><br />
                <input class="buttonS" type="submit" value="Usuń przestępstwo">
            </form>
        </div>
        <div id = "formularzModyfikujOskarzonego">
            <form action="modyfikujOskarzonego.php" method="POST">
            Wybierz oskarżonego:
            <select name="oskarzony_id">
            <?php 
                  foreach($oskarzeni_tab as $element) {
                            $id_imie_nazwisko = $element['imie']." ".$element['nazwisko'];
                            echo '<option value="'.$element['id'].'">'.$id_imie_nazwisko.'</option>';
                        } 
                ?>
            </select> <br/>
            Imię: <?php echo "<div class='error'>"; if(isset($_SESSION['imieO_modERROR'])) {echo $_SESSION['imieO_modERROR']; unset($_SESSION['imieO_modERROR']);} echo "</div>"; ?>
            <input type="text" name="imieO_mod" value="<?php if(isset($_SESSION['imieO_mod'])) { echo $_SESSION['imieO_mod']; unset($_SESSION['imieO_mod']); } else { echo ''; } ?>">
             <br /> 
            Nazwisko:  <?php echo "<div class='error'>"; if(isset($_SESSION['nazwiskoO_modERROR'])) {echo $_SESSION['nazwiskoO_modOERROR']; unset($_SESSION['nazwiskoO_modERROR']);} echo "</div>";  ?> 
            <input type="text" name="nazwiskoO_mod" value="<?php if(isset($_SESSION['nazwiskoO_mod'])) { echo $_SESSION['nazwiskoO_mod']; unset($_SESSION['nazwiskoO_mod']);} else {echo ''; } ?>">
           <br />
            Adwokat: 
            <select name="adwokat_id_mod_osk">
            <?php 
                foreach($adwokaci_tab as $adwokat) {
                    $imie_nazwisko = $adwokat['imie']." ".$adwokat['nazwisko'];
                    echo '<option value="'.$adwokat['id'].'">'.$imie_nazwisko.'</option>';
                } 
            ?>
            </select><br />
            <input class="buttonS" type="submit" value="Modyfikuj">
            </form>
           
        </div>
        <div id = "formularzModyfikujPrzestepstwo">
            <form action="modyfikujPrzestepstwo.php" method="POST">
            Wybierz przestępstwo:
            <select name="przestepstwo_id_mod">
            <?php 
                foreach($przestepstwa_tab as $przestepstwa) {
                    $numer_typ = $przestepstwa['typ'].", ".$przestepstwa['data_przestepstwa'];
                    echo '<option value="'.$przestepstwa['id'].'">'.$numer_typ.'</option>';
                } 
            ?>
            </select><br />
            Zdarzenie: <?php echo "<div class='error'>"; if(isset($_SESSION['typ_modERROR'])) {echo $_SESSION['typ_modERROR']; unset($_SESSION['typ_modERROR']);} echo "</div>"; ?>
            <input type="text" name="typ_mod" value="<?php if(isset($_SESSION['typ_mod'])) { echo $_SESSION['typ_mod']; unset($_SESSION['typ_mod']); } else { echo ''; } ?>">
             <br /> 
        
            Motyw:<?php echo "<div class='error'>"; if(isset($_SESSION['motyw_modERROR'])) {echo $_SESSION['motyw_modERROR']; unset($_SESSION['motyw_modERROR']);} echo "</div>";  ?>
             <input type="text" name="motyw_mod" value="<?php if(isset($_SESSION['motyw_mod'])) { echo $_SESSION['motyw_mod']; unset($_SESSION['motyw_mod']);} else {echo ''; } ?>">
             <br />
       
            Data: <input type="date" name="data_przestepstwa" required> <br />
        
            Miasto:  <?php echo "<div class='error'>"; if(isset($_SESSION['miasto_modERROR'])) {echo $_SESSION['miasto_modERROR']; unset($_SESSION['miasto_modERROR']);} echo "</div>"; ?> 
            <input type="text" name="miasto_mod" value="<?php if(isset($_SESSION['miasto_mod'])) { echo $_SESSION['miasto_mod']; unset($_SESSION['miasto_mod']); } else { echo ''; } ?>">
           <br /> 
        
            Kraj:  <?php echo "<div class='error'>"; if(isset($_SESSION['kraj_modERROR'])) {echo $_SESSION['kraj_modERROR']; unset($_SESSION['kraj_modERROR']);} echo "</div>";  ?> 
            <input type="text" name="kraj_mod" value="<?php if(isset($_SESSION['kraj_mod'])) { echo $_SESSION['kraj_mod']; unset($_SESSION['kraj_mod']);} else {echo ''; } ?>">
           <br />
        
            <input class="buttonS" type="submit" value="Modyfikuj">
            </form>
            
        </div>
        <div id = "formularzModyfikujWyrok">
            <form action="modyfikujWyrok.php" method="POST">
                Wybierz sprawcę:
                <select name="sprawca_id_modW">
                    <?php 
                        foreach($oskarzeni_tab as $przestepstwa) {
                            $numer_typ = $przestepstwa['imie']." ".$przestepstwa['nazwisko'];
                            echo '<option value="'.$przestepstwa['id'].'">'.$numer_typ.'</option>';
                        } 
                    ?>
                </select><br />
                Nr ID sprawy:<?php echo "<div class='error'>"; if(isset($_SESSION['numer_sprawy_modWERROR'])) {echo $_SESSION['numer_sprawy_modWERROR']; unset($_SESSION['numer_sprawy_modWERROR']);} echo "</div>"; ?>
                 <input type="text" name="numer_sprawy_modW" value="<?php if(isset($_SESSION['numer_sprawy_modW'])) { echo $_SESSION['numer_sprawy_modW']; unset($_SESSION['numer_sprawy_modW']); } else { echo ''; } ?>">
                 <br /> 
                Status winy: 
                <select name="status_winy_modW">
                    <option value="1">Winny</option>
                    <option value="2">Niewinny</option>
                    <option value="3">Postępowanie w toku</option>
                </select><br />
                Klasyfikacja wyroku: <?php echo "<div class='error'>"; if(isset($_SESSION['klasyfikacja_modWERROR'])) {echo $_SESSION['klasyfikacja_modWERROR']; unset($_SESSION['klasyfikacja_modWERROR']);} echo "</div>"; ?>
                <select name="klasyfikacja_modW">
                    <option value="1">Brak</option>
                    <option value="2">Kara pieniężna</option>
                    <option value="3">Wyrok w zawieszeniu</option>
                    <option value="4">Kara więzienna lekka - do 1 roku</option>
                    <option value="5">Kara więzienna średnia - od 1 roku - 10 lat</option>
                    <option value="6">Kara więzienna wysoka - od 10 lat</option>
                    <option value="7">Kara śmierci</option>
                </select> <br />
                Więzienie: <?php echo "<div class='error'>"; if(isset($_SESSION['wiezienie_modWERROR'])) {echo $_SESSION['wiezienie_modWERROR']; unset($_SESSION['wiezienie_modWERROR']);} echo "</div>"; ?>
                <select name="wiezienie_id_modW">
                    <option value="0">Brak</option>
                    <?php 
                    foreach($wiezienie_tab as $wiezienie) {
                        if($wiezienie['id']!=0){
                        $miasto_kraj_typ = $wiezienie['miasto'].", ".$wiezienie['kraj']." - ".$wiezienie['typ'];
                        echo '<option value="'.$wiezienie['id'].'">'.$miasto_kraj_typ.'</option>';
                    }
                    } 
                    ?>
                </select> <br />
                <input class="buttonS" type="submit" value="Modyfikuj">
            </form>
        </div>
    </div>

    <div id="temp"></div>
    
    <div style="clear:both;"></div>
    
</body>
</html>
