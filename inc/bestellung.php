<?php

class bestellung{
    private $datum;

    public function __construct(){
        $this->datum = timestamp();
        $DB = new DB();
        $query="INSERT INTO Tagesestellung (datum,gesamtpreis,bemerkungen) VALUES(?,?,?)";
        $this->best_id = $DB->execute_values($query, array($this->datum, 0.0, ""));
    }
    
    public function registerArticle($art_id){
        $query;
    }
    
}