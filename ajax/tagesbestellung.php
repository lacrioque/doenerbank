<?php
session_start();
ini_set('display_errors', '0');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '123455678');
require_once("../inc/defines.php");
include("../inc/database.php");
include("../inc/artikel.php");
include("../inc/einzelbestellung.php");
if(!isset($_SESSION['loggedin'])){die(json_encode(array("success"=>"false")));}
$values = $_GET;
$out = "";
if($values['bestellung']=='uebersicht'){
	$einzelbestellung = new einzelbestellung( $_SESSION['best_id'], $_SESSION['user_id']);
        $einzelbestellung->clean_articles();
        varDump($_SESSION);
	$artikel = $einzelbestellung->getArtikel();
	$gesamtPreis = $einzelbestellung->getGesamtPreis();
	$out = json_encode(array('success' => true, "gesamtPreis"=>$gesamtPreis, "artikelarray" => array('artikel'=>$artikel)));
}

die($out);