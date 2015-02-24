<?php

$values = $_GET;

if($values['register'] == true && $values['timelock'] == false){
	$test = user::registerNew($values['user'], $values['pass']);
	if ($test === true){
		die(
				json_encode(
				array(
					'success'=>'true',
					'message'=>'Willkommen in der Doenerbank! <br> Gleich können sie sich einloggen',
					'time' => time()
					)
						)
				);
	}
} else {
	die(
			json_encode(
				array(
					'success'=>'false',
					'message'=> !$values['timelock'] ? "Bitte noch etwas warten ein falscher Anmeldeversuch liegt zu nah." : "tut mir Leid da ist etwas schief gegangen.",
					'time' => time()
					)
						)
				);
	
}