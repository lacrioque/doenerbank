<?php
require_once("../inc/defines.php");
include("../inc/database.php");
$values = $_GET;
$out = "";
if($values['register'] == true && $values['timelock'] == "false"){
	$DB = new DB();
	$query = "INSERT INTO doener_nutzer ( name , passwort ) VALUES ( ? , ? )";
	$newID = $DB->insert_values($query, array($values['user'],DB::crypt($values['pass'])));
	
	$test = $newID !== false ? true : false;
	if ($test === true){
		$out = json_encode(
				array(
					'success'=>'true',
					'message'=>'Willkommen in der Doenerbank! <br> Gleich können sie sich einloggen',
					'time' => time()
					)
						);
	}
} else {
	$out= json_encode(
				array(
					'success'=>'false',
					'message'=> !$values['timelock'] ? "Bitte noch etwas warten ein falscher Anmeldeversuch liegt zu nah." : "Tut uns sehr Leid da ist etwas schief gegangen.",
					'time' => time()
					)
					);
	
}
//session_destroy();
die($out);
