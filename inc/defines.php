<?php
define('__url', $_SERVER['SERVER_NAME']);
//define('__debug', "an");
define('__debug', "aus");

function varDump($string){
    if(__debug === "an"){
    echo '<pre>'.var_dump($string).'</pre>';
    }
}