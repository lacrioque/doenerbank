<?php
session_start();
define('__url', $_SERVER['SERVER_NAME']);
include 'artikel.php';
include 'bestellung.php';
include 'database.php';
include 'einzelbestellung.php';
include 'user.php';

if(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 7.0;")){
	$header= "<script src='js/jquery-1.11.2.min'></script>"
			. "<script src='js/register.js'></script>";
} else {
	$header= "<script src='js/jquery.min.js'></script>\n"
			. "<script src='js/register.js'></script>";
}