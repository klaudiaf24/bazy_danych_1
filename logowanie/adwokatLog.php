<?php
    session_start();
?>


<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../css/styleLog.css">
    <title>Kancelaria adwokacja</title>
</head>

<body>
    <div id="strG"><a  href="../index.php">Strona Główna</a></div>
    <h1>Panel Adwokata</h1>
<div id="form">
    <form action="adwokatCheck.php" method="POST">
	E-MAIL:<br/> <input type="text" name="email"><?php if(isset($_SESSION['email_error'])) { echo $_SESSION['email_error'].'<br />'; unset($_SESSION['email_error']); } ?>
<br>
        HASŁO:<br/> <input type="password" name="pass"><<?php if(isset($_SESSION['haslo_error'])) { echo $_SESSION['haslo_error'].'<br />'; unset($_SESSION['haslo_error']); } ?>
br>
        <input type="submit" name="submit" value="Zaloguj">
    </form>
</div>

<div id='info'>
<p>Dostępni adwokaci:</p>
            <ol>
                <li>
                    <ul>
                        <li>e-mail : magdawilk@onet.pl</li>
                        <li>hasło : wilczeK88</li>
                    </ul>
                </li>
                <li>
                    <ul>
                        <li>e-mail : andrzej_mekal@o2.pl</li>
                        <li>hasło : andrzej2018</li>
                    </ul>
                </li>
                <li>
                    <ul>
                        <li>e-mail : grzeskowiak_kancelariaAnD@gmail.com</li>
                        <li>hasło : admin123#</li>
                    </ul>
                </li>
                <li>
                    <ul>
                        <li>e-mail : r.zawalski7@onet.pl</li>
                        <li>hasło : cristianoRonaldo7</li>
                    </ul>
                </li>
                <li>
                    <ul>
                        <li>e-mail : basia.lasko9891@o2.pl</li>
                        <li>hasło : czarnySmok</li>
                    </ul>
                </li>
            </ol>
</div>
<div style="clear:both;"></div>
</body>

</html>

