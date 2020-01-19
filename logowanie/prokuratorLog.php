<?php
    session_start();
?>


<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../css/styleLog.css">
    <title>Prokuratura</title>
</head>

<body>
    <div id="strG"><a  href="../index.php">Strona Główna</a></div>
    <h1>Panel Prokuratora</h1>
<div id="form">
    <form action="prokuratorCheck.php" method="POST">
	E-MAIL:<br/> <input type="text" name="email"><?php if(isset($_SESSION['email_error'])) { echo $_SESSION['email_error'].'<br />'; unset($_SESSION['email_error']); } ?>
<br>
        HASŁO:<br/> <input type="password" name="pass"><<?php if(isset($_SESSION['haslo_error'])) { echo $_SESSION['haslo_error'].'<br />'; unset($_SESSION['haslo_error']); } ?>
br>
        <input type="submit" name="submit" value="Zaloguj">
    </form>
</div>

<div id='info'>
<p>Dostępni prokuratorzy:</p>
            <ol>
                <li>
                    <ul>
                        <li>e-mail : annamariawesolowska@wp.pl</li>
                        <li>hasło : annamaria123</li>
                    </ul>
                </li>
                <li>
                    <ul>
                        <li>e-mail : artur.lata90@gmail.com</li>
                        <li>hasło : arturPROKURATOR</li>
                    </ul>
                </li>
                <li>
                    <ul>
                        <li>e-mail : pawel_sobczak@wp.pl</li>
                        <li>hasło : haslo7817</li>
                    </ul>
                </li>
            </ol>
</div>
<div style="clear:both;"></div>
</body>

</html>
