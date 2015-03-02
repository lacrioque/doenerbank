<?php
session_start();
ini_set('display_errors', '0');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '123455678');
require_once("../inc/defines.php");
include("../inc/database.php");
include("../inc/bestellung.php");
include("../inc/artikel.php");
include("../inc/einzelbestellung.php");
if(!isset($_SESSION['loggedin'])){die(json_encode(array("success"=>"false")));}
$values = $_GET;
$einzelbestellung = new einzelbestellung( $_SESSION['best_id'], $_SESSION['user_id']);
$einzelbestellung->clean_articles();
$out = "";

if($values['bestellung']=='uebersicht'){
	$artikel = $einzelbestellung->getArtikel();
	$gesamtPreis = $einzelbestellung->getGesamtPreis();
	$out = json_encode(array('success' => true, "gesamtPreis"=>$gesamtPreis, "artikelarray" => array('artikel'=>$artikel)));
	
} else if($values['bestellung']=='bestaetigen'){
	$einzelbestellung->closeUp();
	
	
} else if($values['bestellung']=='letzte'){
	$retarray = $einzelbestellung->getLetzteBestellung();
	if(empty($retarray['artikelnr'])){
		$out = json_encode(array('success'=> true, "noBest"=>true));
	} else {
		$retarray['success'] = true;
		$retarray['noBest'] = false;
		$out = json_encode($retarray);
	}
}

die($out);