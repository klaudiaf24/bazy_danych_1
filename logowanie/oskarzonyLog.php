<?php
    session_start();
?>


<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../css/styleLog.css">
    <title>Akta oskarżonych</title>
    <style>
    #rej{
        width: auto;
        padding: 10px 18px;
	background-color: #f44336;
        border: none;
        margin: 15px;
    }
    </style>
</head>

<body>
    <div id="strG"><a  href="../index.php">Strona Główna</a></div>
    <h1>Panel Oskarżonego</h1>
    <div id="form">
        <form action="oskarzonyCheck.php" method="POST">
        E-MAIL:<br/> <input type="text" name="email"><?php if(isset($_SESSION['email_error'])) { echo $_SESSION['email_error'].'<br />'; unset($_SESSION['email_error']); } ?><br>
        HASŁO:<br/> <input type="password" name="pass"><?php if(isset($_SESSION['haslo_error'])) { echo $_SESSION['haslo_error'].'<br />'; unset($_SESSION['haslo_error']); } ?><br>
        <input type="submit" name="submit" value="Zaloguj">
        </form>

        <div id="rejestracja">
            Zarejestruj się!<br/>
            <button type="button" id="rej" onclick="getElementById('rejForm').style.display = 'block'; getElementById('rej').style.display = 'none';">Zarejestruj</button>
            <div id="rejForm" style="display: none;">
                <form action="oskarzonyRej.php" method="POST">
                Imię:<br/> <input type="text" name="imieR"><?php if(isset($_SESSION['imieR_error'])) { echo $_SESSION['imieR_error'].'<br />'; unset($_SESSION['imieR_error']); } ?><br>
                Nazwisko:<br/> <input type="text" name="nazwiskoR"><?php if(isset($_SESSION['nazwiskoR_error'])) { echo $_SESSION['nazwiskoR_error'].'<br />'; unset($_SESSION['nazwiskoR_error']); } ?><br>
                e-mail:<br/> <input type="text" name="emailR"><?php if(isset($_SESSION['emailR_error'])) { echo $_SESSION['emailR_error'].'<br />'; unset($_SESSION['emailR_error']); } ?><br>
                hasło:<br/> <input type="password" name="passR"><?php if(isset($_SESSION['hasloR_error'])) { echo $_SESSION['hasloR_error'].'<br />'; unset($_SESSION['hasloR_error']); } ?><br>
                <input type="submit" name="submit" value="Zarejestruj">
                </form>
            </div>
        </div>
    </div>

    <div id='info'>
        <p>Dostępni oskarżeni:</p>
                <ol>
                    <li>
                        <ul>
                            <li>e-mail : krystian.lampa17@wp.pl</li>
                            <li>hasło : sezamki666</li>
                        </ul>
                    </li>
                    <li>
                        <ul>
                            <li>e-mail : justyna_podloga_snk@gmail.com</li>
                            <li>hasło : justysia@!</li>
                        </ul>
                    </li>
                    <li>
                        <ul>
                            <li>e-mail : roman.kringe@gmail.com</li>
                            <li>hasło : romanZdomu</li>
                        </ul>
                    </li>
                    <li>
                        <ul>
                            <li>e-mail : bojan.mira@o2.pl</li>
                            <li>hasło : mirusia_bojan2</li>
                        </ul>
                    </li>
                    <li>
                        <ul>
                            <li>e-mail : katarzyna_kalemb6580@wp.pl</li>
                            <li>hasło : herbata_z_miodem</li>
                        </ul>
                    </li>

                </ol>

    </div>
    <div style="clear:both;"></div>
</body>

</html>

