<?php
// PHP Klasse die den user zu der doenerbank überprüfen soll


class user{

private $user_id;
private $user_pass;
private $user_data;
private $login = false;


/**
 * Konstukter der User Klasse gilt gleichermaßen als Login Klasse
 * Gibt es keinen gültigen User, destructed sicht sie Klasse selbst.
 * 
 * @param String $user Der Username
 * @param String $pass Das Passwort
 */
public function __construct($user, $pass){
    $query = "SELECT id FROM doener_nutzer WHERE user=? and pass=?";
    $database = new DB();
    $result = $database->query_values($query,array($user,DB::crypt($pass)));
    if($result !== false){
        $this->login = true;
        $this->user_id = $result[0];
        $this->user_pass = $pass;
        $this->getUserData();
    } else {
        $this->__destruct();
    }
}

public function saveToSession($keyValueArr){
    foreach($keyValueArr as $key=>$value){
        if(!(empty($key) && empty($value))){
        $_SESSION[$key] = $value;
        }
    }
}    
    
public function getUserData(){
    $query = "SELECT * FROM doener_nutzer WHERE user_id=?";
    $database = new DB();
    $result = $database->query_values($query,array($this->user_id));
    if($result !== false){
        $this->user_data = $result[0];
    }
}

public function getUserBestellungen(){
    $DB = new DB();
    $query_bestid = "SELECT best_id,datum FROM doener_tagesestellung WHERE datum = (SELECT MAX(datum) FROM Tagesestellung)";
    $best_id = $DB->query($query_bestid);
    $query_ebestid = "SELECT ebest_id FROM doener_einzelbestellung  WHERE user_id=? and best_id=?";
    $ebest_ids = $DB->query_values($query_ebestid,array($this->user_id, $best_id[0]['best_id']));
    $query_bestellung = "SELECT 
        art.name as artikelname, 
        art.preis as artikelpreis, 
        art.kategorie as artikelkategorie, 
        art.beschreibung as artikelbeschreibung
        FROM doener_artikelliste al 
        JOIN doener_artikel art 
        ON al.art_id = art.art_id
        WHERE ebest_id=?";
        $artikel=array();
    foreach($ebest_ids[0] as $ebest_id){
        $artikel[]=$DB->query_values($query_bestellung, array($ebest_id));
    }
    $returner = array("datum"=>$best_id[0]['datum'], "artikel"=>$artikel);
    return $returner;
}

}