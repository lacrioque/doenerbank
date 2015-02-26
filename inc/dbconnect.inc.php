<?php

/**
 * @author Steffen Pfeil
 * @copyright 2015
 * @name Database Connection
 */

$dbname = "doener";
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";

$connection = mysqli_connect($dbhost,$dbuser,$dbpass); 
if($connection){
    mysqli_select_db($connection, $dbname);
}else{
    echo '
    <div class="alert">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>Warning!</strong> Konnte nicht mit Datenbank verbinden.
    </div>
    ';
    
}




?>