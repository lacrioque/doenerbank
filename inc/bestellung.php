<?php

class bestellung{
    private $datum;
    private $best_id;
    private $best_data;

    public function __construct(){
        $this->datum = $begin = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $DB = new DB();
        $query="SELECT best_id, datum,gesamtpreis,bemerkungen FROM doener_tagesbestellung WHERE datum = ?";
        $result =  $DB->query_values($query, array($this->datum));
        varDump("query bestellung nach datum");
        varDump($result);
        if (!$result){
            $query="INSERT INTO doener_tagesbestellung (datum,gesamtpreis,bemerkungen) VALUES(?,?,?)";
            $this->best_data = array( "datum"=>$this->datum, "gesamtpreis" => 0.0, "bemerkungen" => "");
            $this->best_id = $DB->insert_values($query, $this->best_data);
			varDump("neue Bestellung");
			varDump($this->best_id);
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
	
    public function saveAenderung(){
        $DB = new DB();
        $query = "UPDATE doener_tagesbestellung SET gesamtpreis=?, WHERE best_id = ".$this->best_id;
		$array = array($this->best_data['gesamtpreis']);
        $DB->update_values($query, $array);
    }
    
    public function getArtikelForUser($user_id,$ebest_id){
        $DB = new DB();
        $query_artikel = "SELECT * FROM bestellung_gesamt WHERE Nutzer = ? AND datum = ".$this->datum;
        $mengenQuery = "SELECT art_id,ebest_id,COUNT(art_id) as menge FROM doener_artikelliste WHERE ebest_id = ? AND art_id = ? GROUP BY ebest_id";
        $artikel_array = $DB->query_values($query_artikel, array($user_id));
        foreach ($artikel_array as $i => $artikel){
            $menge = $DB->query_values($mengenQuery, array($ebest_id, $artikel['art_id']));
            $artikel_array[$i]['menge'] = $menge[0]['menge'];
        }
        return $artikel_array;
    }
    
    public function getNutzerBestellungen(){
            $DB = new DB();
            $query_user = "SELECT DISTINCT Nutzer, NutzerGesamtPreis, ebest_id FROM bestellung_gesamt WHERE datum = ".$this->datum;
            $user = $DB->query($query_user);
            $user_article = array();
            foreach ($user as $i=>$nutzer){
                $artikel = $this->getArtikelForUser($nutzer['Nutzer'], $nutzer['ebest_id']);
                    $user_article[] = array(
                            "name" => $nutzer['Nutzer'],
                            'gesamtPreis' => $nutzer['NutzerGesamtPreis'],
                            'ebest_id' => $nutzer['ebest_id'],
                            "artikel" => $artikel
                    );	
            }
            return $user_article;
    }
	
    public function showTagesbestellung(){
        $DB = new DB();
        $query = "SELECT * FROM bestellung_gesamt WHERE datum = ".$this->datum;
        $tagesWerk = $DB->query($query);
        return $tageswerk;
    }
    
	public function getLetzteBestellung(){
		return ($this->best_id-1);
	}
}