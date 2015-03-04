<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
require_once("../inc/defines.php");
include("../inc/database.php");
$values = $_GET;
$out = "";

if($values['register'] == 'true' && $values['timelock'] == "false"){
	
	$DB = new DB();
        $query_test_name = "SELECT name from doener_nutzer";
        $namen = $DB->query($query_test_name);
        $belegt = array();
        foreach($namen as $i=>$name){
            $belegt[] = $name['name'];
        }
        if(in_array($values['user'],$belegt,true)){
            $out= json_encode(
				array(
					'success'=>'false',
					'message'=> 'Dieser Benutzername ist schon vergeben, bitte einen anderen aussuchen.',
					'time' => time()
					)
					);
            die($out);
        }
	$query = "INSERT INTO doener_nutzer ( name , passwort, email ) VALUES ( ?, ?, ?)";
	$newID = $DB->insert_values($query, array($values['user'],DB::crypt($values['pass']), $values['email']));
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
