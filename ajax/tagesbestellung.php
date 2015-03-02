<?php
session_start();
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
        $einzelbestellung->getArtikellisten();
        varDump($einzelbestellung);
	$artikel = $einzelbestellung->getArtikel();
	$out = json_encode(array('success' => true, "artikelarray" => array('artikel'=>$artikel)));
}

die($out);