<?php

class bestellung{
    private $datum;
    private $best_id;
    private $best_data;

    public function __construct(){
        $this->datum = $begin = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $DB = new DB();
        $query="SELECT best_id, datum,gesamtpreis,bemerkungen FROM doener_tagesestellung WHERE datum = ?";
        $result =  $DB->query_values($query, array($this->datum));
        varDump("query bestellung nach datum");
        varDump($result);
        if (!$result){
            $query="INSERT INTO doener_tagesestellung (datum,gesamtpreis,bemerkungen) VALUES(?,?,?)";
            $this->best_data = array( "datum"=>$this->datum, "gesamtpreis" => 0.0, "bemerkungen" => "");
            $this->best_id = $DB->insert_values($query, $this->best_data);
        } else {
            $this->best_data = $result[0];
            $this->best_id = $this->best_data['best_id'];
        }
    }
    
	public function getBestId(){
		return $this->best_id;
	}
	
    public function gesamtpreisErhoehen($preisDazu){
        if (is_numeric($preisDazu)){
            $this->best_data["gesamtpreis"] += $preisDazu;
            $this->saveAenderung();
            return true;
        } else {
            return false;
        }
    }
    
	public function bemerkungDazu($bemerkung){
		$this->best_data['bemerkungen'] += $bemerkung."|";
		$this->saveAenderung();
	}
	
    public function saveAenderung(){
        $DB = new DB();
        $query = "UPDATE doener_tagesestellung SET gesamtpreis=?, bemerkungen=? WHERE best_id = ".$this->best_id;
		$array = array($this->best_data['gesamtpreis'],$this->best_data['bemerkungen']);
        $DB->update_values($query, $array);
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
    
	public function getLetzteBestellung(){
		return ($this->best_id-1);
	}
	
	public function showBemerkungen($wann=false){
		if(!$wann){
			$bemerkungen_formatted = explode("|", $this->best_data['bemerkungen']);
			return $bemerkungen_formatted;
		} else {
			$DB = new DB();
			$query = "SELECT bemerkungen FROM doener_tagesestellung WHERE datum = ?";
			$bemerkungen = $DB->query_values($query, array($wann));
			$bemerkungen_formatted = empty($bemerkungen) || !$bemerkungen ? "" : explode("|", $bemerkungen);
			return $bemerkungen_formatted;
		}
		
	}
}