<?php
session_start();
if(!isset($_SESSION['user_id']) && $_SESSION['user']['admin'] === 1){
die('{"success": "false", "message": "Nicht eigeloggt, bitte einloggen!"}');
}
ini_set('display_errors', '0');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '123455678');
require_once("../inc/defines.php");
include("../inc/database.php");
include("../inc/einzelbestellung.php");
include("../inc/user.php");
include("../inc/artikel.php");
include("../inc/bestellung.php");
$values = $_GET;
$out = "";
$DB = new DB();
$bestellung = new bestellung();
if($values['admin']=="schauschau"){
	$out = "";
	$bestellungen = $bestellung->getNutzerBestellungen();
	$user_query = "SELECT * FROM doener_nutzer";
	$user = $DB->query($user_query);
	die(json_encode(array("success"=>true, "users"=>$user, "orders"=>$bestellungen)));

} else if($values['admin']=="dennichtmehr") {
    $deleteQuery = "DELETE FROM doener_nutzer WHERE user_id = ?";
    $test = $DB->update_values($deleteQuery, array($values['uid']));
    $out = !$test ? json_encode(array("success"=> false)) : json_encode(array("success"=> true));
    die($out);
} else if($values['admin']=="deristtoll") {
    $newAdmin = new user($values['uid']);
    $test = $newAdmin->setAdmin();
    $out = !$test ? json_encode(array("success"=> false)) : json_encode(array("success"=> true));
    die($out);
} else if($values['admin']=="nichtmehrtoll") {
    $noMoreAdmin = new user($values['uid']);
    $test = $noMoreAdmin->setAdmin();
    $out = !$test ? json_encode(array("success"=> false)) : json_encode(array("success"=> true));
    die($out);
} else if($values['admin']=="dasnicht") {
	
} else if($values['admin']=="gutistfuerheute") {
	
} else if($values['admin']=="allesmist") {
	
}