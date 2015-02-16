<?php

class bestellung{
    private $datum;
    private $best_id;
    private $best_data;

    public function __construct(){
        $this->datum = timestamp();
        $DB = new DB();
        $query="INSERT INTO doener_tagesestellung (datum,gesamtpreis,bemerkungen) VALUES(?,?,?)";
        $this->best_id = $DB->execute_values($query, array($this->datum, 0.0, ""));
        $this->best_data = array( "datum"=>$this->datum, "gesamtpreis" => 0.0, "bemerkungen" => "");
    }
    
    public function gesamtpreisErhoehen($preisDazu){
        if (is_numeric($preisDazu)){
            $this->best_data["gesamtpreis"] += $preisDazu;
            return true;
        } else {
            return false;
        }
    }
    
    
    
    public function showTagesbestellung(){
        $DB = new DB();
        $query = "SELECT a.name as Artikelname,a.preis as Artikelpreis
        n.name Nutzer, tb.datum as datum, eb.preis, eb.ebest_id as NutzerGesamt 
        FROM doener_tagesbestellung tb
        JOIN doener_einzelbestellung eb ON tb.best_id = eb.best_id
        JOIN doener_nutzer n ON eb.user_id = n.user_id
        JOIN doener_artikelliste al ON eb.ebest_id = al.ebest_id
        JOIN doener_artikel a ON al.art_id = a.art_id
        ORDER BY ebest_id";
        
        $tagesWerk = $DB->query($query);
        return $tageswerk;
    }
    
}