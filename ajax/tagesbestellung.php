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
$out = "";

if($values['bestellung']=='uebersicht'){
	$einzelbestellung->clean_articles();
	$artikel = $einzelbestellung->getArtikel();
	$gesamtPreis = $einzelbestellung->getGesamtPreis();
	$out = json_encode(array('success' => true, "gesamtPreis"=>$gesamtPreis, "artikelarray" => array('artikel'=>$artikel)));
	
} else if($values['bestellung']=='bestaetigen'){
	$artikelDaten = json_decode($_POST['artikel'],true);
	foreach($artikelDaten as $artikel){
		$einzelbestellung->finalizeArticle($artikel['art_id'], $artikel['bemerkung'], $artikel['menge']);
	}
	$einzelbestellung->closeUp();
	$out = json_encode(array("success"=> true));
	
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