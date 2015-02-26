<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '123455678');
require_once("../inc/defines.php");
include("../inc/database.php");
$values = $_GET;
$out = "";

if($values['artikel']=='alle'){
	$DB = new DB();
	$query = "SELECT art_id, name, preis, kategorie, beschreibung FROM doener_artikel ORDER BY kategorie";
	$artikel = $DB->query($query);
	$out = json_encode($artikel);
}
else if($values['artikel']=='einige'){
        $einige_artikel = explode('|',$values['art_ids']);
	$DB = new DB();
	$query = "SELECT art_id, name, preis, kategorie, beschreibung FROM doener_artikel WHERE art_id IN (".join(',',$einige_artikel).") ORDER BY kategorie";
	$artikel = $DB->query($query);
        $out_data = array();
        foreach($artikel as $i => $artikel_einzel){
            $out_data[] = $artikel_einzel;
        }
	$out = json_encode(array('success' => true, "artikelarray" => array('artikel'=>$out_data)));
}

die($out);