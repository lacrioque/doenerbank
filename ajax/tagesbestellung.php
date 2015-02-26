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
global $bestellung;
if($values['bestellung']=='userconfirm'){
        $einige_artikel = explode('|',$values['art_ids']);
	$einzelbestellung = new einzelbestellung();
	$artikel = $DB->query($query);
        $out_data = array();
        foreach($artikel as $i => $artikel_einzel){
            $out_data[] = $artikel_einzel;
        }
	$out = json_encode(array('success' => true, "artikelarray" => array('artikel'=>$out_data)));
}

die($out);