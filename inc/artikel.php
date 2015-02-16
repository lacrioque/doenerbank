<?php

class artikel{

    private $art_id;
    private $name;
    private $preis;
    private $kategorie;
    private $beschreibung;
    
    public function __construct($art_id){
        $DB = new DB();
        $query = "SELECT * FROM Artikel WHERE art_id = ?";
        $artikelDaten = $DB->query_values($query, array($art_id));
        $this->art_id =  $art_id;
        $this->name = $artikelDaten[0]['name'];
        $this->preis = $artikelDaten[0]['preis'];
        $this->kategorie = $artikelDaten[0]['kategorie'];
        $this->beschreibung = $artikelDaten[0]['beschreibung'];
    }
    
    public function preis(){ return $this->preis; }
    public function name(){ return $this->name; }
    public function beschreibung() {return $this->beschreibung; }
    public function kategorie() { return $this->kategorie; }

}