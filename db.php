<?php
   $dbname      = "dbname = u7fil";
   $credentials = "user = u7fil password=7fil";

   $db = pg_connect( "$dbname $credentials"  );
   if(!$db) {
      echo "Error : Brak połączenia z bazą danych\n";
   }
?>
