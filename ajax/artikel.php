<?php
session_start();
if(!isset($_SESSION['user_id'])){
die('{"success": "false", "message": "Nicht eigeloggt, bitte einloggen!"}');
}
//ini_set('display_errors', '1');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '123455678');
require_once("../inc/defines.php");
include("../inc/database.php");
include("../inc/einzelbestellung.php");
include("../inc/artikel.php");
include("../inc/bestellung.php");
$values = $_GET;
$out = "";
$DB = new DB();
$objBestellung = new einzelbestellung( $_SESSION['best_id'], $_SESSION['user_id']);
if($values['artikel']=='alle'){
	$query = "SELECT art_id, name, preis, kategorie, beschreibung FROM doener_artikel ORDER BY kategorie";
	$artikel = $DB->query($query);
	$out = json_encode($artikel);
}
else if($values['artikel']=='einige'){
    $einige_artikel = explode('|',$values['art_ids']);
        if($values['art_ids'] == "|null" || $values['art_ids'] == 'null|'){
            $out = json_encode(array('success' => true, "gesamtPreis"=>0.0, "artikelarray" => array()));
        }
        $objBestellung->reset_artikel();
        foreach($einige_artikel as $i=>$artikel){
            if($artikel == "" || $artikel == 'null' || $artikel == false){ 
                unset($einige_artikel[$i]); continue; 
            } else {
                $artikellisten[] = $objBestellung->registerArticle($artikel);
            }
        }
        varDump($artikellisten);
	$query = "SELECT art_id, name, preis, kategorie, beschreibung FROM doener_artikel WHERE art_id IN (".join(',',$einige_artikel).") ORDER BY kategorie";
	$artikel = $DB->query($query);
//        $out_data = array();
//        foreach($artikel as $i => $artikel_einzel){
//            $out_data[] = $artikel_einzel;
//        }
        $gesamtPreis = $objBestellung->saveAenderung();
	$out = json_encode(array('success' => true, "gesamtPreis"=>$gesamtPreis, "artikelarray" => array('artikel'=>$artikel)));

        
} else if($values['artikel']=='confirm'){
    $artikel = explode('|',$values['korb']);
        foreach($einige_artikel as $i=>$artikel){
            if($artikel === "" || $artikel == 'null' || $artikel == false){ 
                unset($einige_artikel[$i]); continue; 
            } else {
                $artikellisten[] = $objBestellung->registerArticle($artikel);
            }
        }
    $gesamtPreis = $objBestellung->saveAenderung();
    $out = json_encode(array('success' => true, "gesamtPreis"=>$gesamtPreis, "artikellisten" => $artikellisten));
} else if($values['artikel']=='clear'){
    $objBestellung->clear_articles();
    $out=json_encode(array("success" => true, "message"=> "Bestellungen zurückgesetzt"));
}

die($out);