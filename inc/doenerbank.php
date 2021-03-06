<?php
ini_set('display_errors', 1);
session_start();
require_once("defines.php");


include 'artikel.php';
include 'bestellung.php';
include 'database.php';
include 'einzelbestellung.php';
include 'user.php';

$header = '	<link href="css/bootstrap.min.css" rel="stylesheet">
			<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
			<link href="css/bootstrap-switch.min.css" rel="stylesheet">
			<link href="css/bootstrap-dialog.css" rel="stylesheet">
			<link href="css/main.css" rel="stylesheet" type="text/css"/>
			<link href="css/login.css" rel="stylesheet" type="text/css"/>
			<link href="css/nav.css" rel="stylesheet" type="text/css"/>';

if(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 7.0;")){
	$header.= "<script src='js/jquery-1.11.2.min'></script>";
} else {
	$header.= "<script src='js/jquery.min.js'></script>\n";
}
$header .="<script src='js/checkLoginForm.js'></script>\n"
		. "<script src='js/bootstrap.min.js'></script>\n"
		. "<script src='js/bootstrap-switch.min.js'></script>\n"
		. "<script src='js/register.js'></script>\n"
		. "<script src='js/mustache.js'></script>\n"
		. "<script src='js/bootstrap-dialog.js'></script>\n"
		. "<script src='js/Artikel.js'></script>\n"
		. "<script src='js/warenkorb.js'></script>\n"
		. "<script src='js/administration.js'></script>\n"
		. "<script src='js/uebersicht.js'></script>\n"
		. "<script src='js/index.js'></script>";
