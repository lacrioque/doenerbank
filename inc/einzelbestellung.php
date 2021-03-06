<?php

class einzelbestellung {
    private $best_id;
    private $ebest_id;
    private $ebest_preis;
    private $artlist_ids = array();
	private $geschlossen = false;

    public function __construct($best_id, $user_id){
        $DB = new DB();
        $this->best_id = $best_id;
        $query_ebest = "SELECT ebest_id,ebest_preis,bestaetigt FROM doener_einzelbestellung WHERE best_id = ? AND user_id = ?";
        $return = $DB->query_values($query_ebest, array($best_id, $user_id));
        varDump("Einzelbestellung");
        varDump($return);
        if($return == false){
            $this->ebest_preis = 0.0;
			if ($_SESSION['bestellung']['geschlossen']==true){
				$query = "INSERT INTO doener_einzelbestellung (user_id,best_id,bestaetigt) VALUES(?,?, '1')";
				$this->ebest_id = $DB->insert_values($query, array($user_id, $best_id, $this->ebest_preis));
			} else {
				$query = "INSERT INTO doener_einzelbestellung (user_id,best_id) VALUES(?,?)";
				$this->ebest_id = $DB->insert_values($query, array($user_id, $best_id, $this->ebest_preis));
			}
            varDump(array($this->ebest_id, $user_id, $best_id, $this->ebest_preis));
        } else {
            $this->ebest_id = $return[0]['ebest_id'];
			$this->geschlossen = $return[0]['bestaetigt'] == 1 ? true : false;
			if($_SESSION['user']['admin'] == true){ $this->geschlossen = false;}
        }
        
    }
    
	public function openUp(){
		$DB = new DB();
		$query_open = "UPDATE doener_einzelbestellung SET bestaetigt = '0' WHERE ebest_id = ".$this->ebest_id;
		$test = $DB->update($query_open);
		return $test !== false ? true : false;
	}
	public function closeUp(){
		//$artikel = $this->
		$DB = new DB();
		$query_close = "UPDATE doener_einzelbestellung SET bestaetigt = '1' WHERE ebest_id = ".$this->ebest_id;
		$test = $DB->update($query_close);
		return $test !== false ? true : false;
	}
	
    public function getArtikellisten(){
		$this->artlist_ids = array();
        $DB = new DB();
        $query_artlist = "SELECT artlist_id FROM doener_artikelliste WHERE ebest_id = ?";
        $preresult = $DB->query_values($query_artlist, array($this->ebest_id));
        varDump($preresult);
        foreach($preresult as $i=>$result_arr){
            $this->artlist_ids[] = $result_arr['artlist_id'];
        }
		return $this->artlist_ids;
    }
    
    public function getArtikel(){
		$artikel_arr = array();
        foreach ($this->artlist_ids as $i => $artikelliste){
            $artikel = new artikel($this->getArtikelausArtListID($artikelliste));
            $artikel_arr[] = $artikel->getArtikelData();
        }
        return $artikel_arr;
    }
	
    public function getArtikelIds(){
        foreach ($this->artlist_ids as $i => $artikelliste){
            $artikel = $this->getArtikelausArtListID($artikelliste);
            $artikel_arr[] = $artikel;
        }
        return $artikel_arr;
    }
    
	public function isGeschlossen(){
		return $this->geschlossen;
	}
	
    public function registerArticle($art_id, $bemerkung = false){
		if($this->geschlossen){return array("geschlossen"=>true);}
        $DB = new DB();
        $artikel = new Artikel($art_id);
        $this->preis_erhoehen($artikel->preis());
        if($bemerkung === false){
            $query_artlist = "INSERT INTO doener_artikelliste (ebest_id, art_id) VALUES(?, ?)";
            $artlist_id = $DB->insert_values($query_artlist, array($this->ebest_id, $art_id));
        } else {
            $query_artlist = "INSERT INTO doener_artikelliste (ebest_id, art_id, bemerkungen) VALUES(?, ?, ?)";
            $artlist_id = $DB->insert_values($query_artlist, array($this->ebest_id, $art_id, $bemerkung));
        }
        varDump("RegisterArticle");
        varDump($artlist_id);
        array_push($this->artlist_ids,$artlist_id);
        return $artlist_id;
    }
    
	public function finalizeArticle($art_id, $bemerkung, $menge){
            varDump($art_id, $bemerkung, $menge);
		$DB = new DB();
		$artikellisten = $this->getArtikellisten();
		foreach($artikellisten as $artikelliste){
			$artikel_id = $this->getArtikelausArtListID($artikelliste);
			if(($art_id == $artikel_id)){
				$query_artlist = "UPDATE doener_artikelliste SET bemerkungen = ? WHERE artlist_id = ?";
				$DB->update_values($query_artlist, array($bemerkung, $artikelliste));
				if($menge>1){
                                        for(;$menge>0;$menge--){
						$this->registerArticle($art_id,$bemerkung);
					}
				}
                        } else { continue; }
		}
		$this->saveAenderung();
	}
	
    public function unregisterArticle($artlist_id){
		if($this->geschlossen){return array("geschlossen"=>true);}
        $DB = new DB();
        $artikel = new artikel($this->getArtikelausArtListID($artlist_id));
        $this->preis_vermindern($artikel->preis());
        $query_artlist = "DELETE FROM doener_artikelliste WHERE artlist_id = ?";
        $result = $DB->update_values($query_artlist, array($artlist_id));
        
    }
    
	public function getLetzteBestellung(){
		$bestellung = new bestellung();
		$lastBestID = $bestellung->getLetzteBestellung();
		$lastEbest = new einzelbestellung($lastBestID, $_SESSION['user_id']);
		$lastEbest->getArtikellisten();
		$retArray = array('artikelnr' =>$lastEbest->getArtikel(), 'gesamtPreis'=>$lastEbest->getGesamtPreis());
		unset($lastEbest);
		return $retArray;
		
	}
	
    public function clean_articles(){
		if($this->geschlossen){return array("geschlossen"=>true);}
        $this->getArtikellisten();
        $preis = 0.0;
        foreach ($this->artlist_ids as $i => $artikelliste){
            $artikel = new artikel($this->getArtikelausArtListID($artikelliste));
            $preis +=$artikel->preis();
        }
        $this->ebest_preis = $preis;
    }
    
    public function reset_artikel(){
		if($this->geschlossen){return array("geschlossen"=>true);}
        foreach ($this->artlist_ids as $i => $artikelliste){
            $this->unregisterArticle($artikelliste);
        }
        $this->artlist_ids = array();
        $this->ebest_preis = 0.0;
    }
    
    public function clear_articles(){
		if($this->geschlossen){return array("geschlossen"=>true);}
        foreach ($this->artlist_ids as $i => $artikelliste){
            $this->unregisterArticle($artikelliste);
        }
        $this->artlist_ids = array();
        $this->ebest_preis = 0.0;
        $this->saveAenderung();
    }

    private function preis_vermindern($preisVermindern){
        if (is_numeric($preisVermindern)){
            $this->ebest_preis -= $preisVermindern;
            return true;
        } else {
            return false;
        }
    }
    
    private function preis_erhoehen($preisDazu){
        if (is_numeric($preisDazu)){
            $this->ebest_preis += $preisDazu;
            return true;
        } else {
            return false;
        }
    }
    
    public function getArtikelausArtListID($artlist_id){
        $DB = new DB();
        $query = "SELECT art_id FROM doener_artikelliste WHERE artlist_id = ?";
        $artikel = $DB->query_values($query, array($artlist_id));
        return $artikel[0]['art_id'];
    }
	
	public function getGesamtPreis(){
		return $this->ebest_preis;
	}
	
	public function saveAenderung(){
		if($this->geschlossen){return array("geschlossen"=>true);}
        $DB = new DB();
        $query = "UPDATE doener_einzelbestellung SET ebest_preis=? WHERE ebest_id = ".$this->ebest_id;
        $DB->update_values($query, array($this->ebest_preis));
        return $this->ebest_preis;
    }
}