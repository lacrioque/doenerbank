<?php

class bestellung{
    private $datum;
    private $best_id;
    private $best_data;
	private $geschlossen;

    public function __construct(){
        $this->datum = $begin = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $DB = new DB();
        $query="SELECT best_id, datum,gesamtpreis,bemerkungen,closed FROM doener_tagesbestellung WHERE datum = ?";
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
			$this->geschlossen = $result[0]['closed'];
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
        $artikel_array = $DB->query_values($query_artikel, array($user_id));
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

    public function getDatumString(){
        $datum = date("d.m.Y", $this->datum);
        return $datum;
    }
    public function getDatumFileString(){
        $datum = date("d_m_y", $this->datum);
        return $datum;
    }
    
	public function istGeschlossen(){
		return $this->geschlossen == 1 ? true : false ;
	}
	
	public function close(){
		$DB = new DB();
		$closeQuery  = "UPDATE doener_tagesbestellung SET closed = '1' WHERE best_id = ".$this->best_id;
		$test = $DB->update($closeQuery);
		return $test;
	}
	
    public function closeAll(){
        $DB = new DB();
		$this->close();
        $ebest_query =  $query = "SELECT ebest_id FROM bestellung_gesamt WHERE datum = ".$this->datum;
        $ebests = $DB->query($ebest_query);
        $ebest_closeQuery = "UPDATE doener_einzelbestellung SET bestaetigt = '1' WHERE ebest_id = ?";
        foreach($ebests as $i=>$ebest_id){
            $test = $DB->update_values($ebest_closeQuery, array($ebest_id['ebest_id']));
            if($test === false) {return false;}
        }
        return true;
    }
    
    public function deleteEinzel($ebestID){
        $DB = new DB();
        $query_delete = "DELETE FROM doener_einzelbestellung WHERE ebest_id = ?";
        $returner = $DB->update_values($query_delete, array($ebestID));
        return $returner;
    }
    
    public function showTagesbestellung(){
        $DB = new DB();
        $query = "SELECT * FROM bestellung_gesamt WHERE datum = ".$this->datum." ORDER BY Nutzer";
        $tagesWerk = $DB->query($query);
        return $tagesWerk;
    }
    
	public function getLetzteBestellung(){
		return ($this->best_id-1);
	}
}