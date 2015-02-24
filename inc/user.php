<?php
// PHP Klasse die den user zu der doenerbank überprüfen soll


class user{

private $user_id;
private $user_pass;
private $user_data;
private $login = false;
private $time;

/**
 * Konstukter der User Klasse gilt gleichermaßen als Login Klasse
 * Gibt es keinen gültigen User, destructed sicht sie Klasse selbst.
 * 
 * @param String $user Der Username
 * @param String $pass Das Passwort
 */
public function __construct($user, $pass, $session = false){
	if($session!== false){
		$query = "SELECT user_id,passwort,name FROM doener_nutzer WHERE loggedIn = ?";
		$array = array($session);
	} else {
		$query = "SELECT user_id,passwort,name FROM doener_nutzer WHERE name = ? and passwort = ?";
		$array = array($user,DB::crypt($pass));
	}
    $DB = new DB();
    $result = $DB->query_values($query,$array);
    if($result !== false){
        $this->login = true;
        $this->user_id = $result[0]['user_id'];
        $this->user_pass = $result[0]['passwort'];
        $this->getUserData();
		if(!isset($_SESSION['time'])){
			$this->time = $_SESSION['time'] = time();
			
		} else {
			$this->time = $_SESSION['time'];
		}
		$loginQuery = "UPDATE doener_nutzer SET loggedIn = ? WHERE user_id = ?";
		$DB->update_values($loginQuery, array($this->sessionCrypt(), $this->user_id));
    } else {
        $this->__destruct();
    }
}

public function sessionCrypt(){
	return crypt($this->user_id + $this->user_pass + $this->user_data['name'] + $_SESSION['time']);
}

public function checkLogin(){
	return  $this->login;
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

public static function registerNew($user, $pass){
	$DB = new DB();
	$query = "INSERT INTO doener_nutzer (name,passwort) VALUES(?, ?)";
	$newID = $DB->insert_values($query, array($user,DB::crypt($pass)));
	if($newID !== false){
		return true;
	} else {
		return false;
	}
}
public function __destruct(){
	unset($this->user_id);
	unset($this->user_pass);
	unset($this->user_data);
	unset($this->login);
	unset($this->time);
	session_destroy();
	return NULL;
}
}