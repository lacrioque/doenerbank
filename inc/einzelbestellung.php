<?php

class einzelbestellung {
    private $ebest_id;
    private $ebest_preis;
    private $artlist_ids = array();
    private $debug = true;

    public function __construct($best_id, $user_id){
        $DB = new DB();
        $query_ebest = "SELECT ebest_id FROM doener_einzelbestellung WHERE best_id = ? AND user_id = ?";
        $return = $DB->query_values($query_ebest, array($best_id, $user_id));
        if($this->debug){varDump($return);}
        if($return === false){
            $this->ebest_preis = 0.0;
            $query = "INSERT INTO doener_einzelbestellung (user_id,best_id) VALUES(?,?)";
            $this->ebest_id = $DB->insert_values($query, array($user_id, $best_id, $this->ebest_preis));
            if($this->debug){varDump(array($this->ebest_id, $user_id, $best_id, $this->ebest_preis));}
        } else {
            $this->ebest_id = $return[0]['ebest_id'];
        }
        
    }
    
    public function getArtikellisten(){
        $DB = new DB();
        $query_artlist = "SELECT artlist_id FROM doener_artikelliste WHERE ebest_id = ?";
        $preresult = $DB->query_values($query_artlist, array($this->ebest_id));
        varDump($preresult);
        foreach($preresult as $i=>$result_arr){
            $this->artlist_ids[] = $result_arr['artlist_id'];
        }
    }
    
    public function getArtikel(){
        foreach ($this->artlist_ids as $i => $artikelliste){
            $artikel = new artikel($this->getArtikelausArtListID($artikelliste));
            $artikel_arr[] = $artikel->getArtikelData();
        }
        return $artikel_arr;
    }
    
    public function registerArticle($art_id){
        $DB = new DB();
        $artikel = new Artikel($art_id);
        $this->preis_erhoehen($artikel->preis());
        $query_artlist = "INSERT INTO doener_artikelliste (ebest_id, art_id) VALUES(?, ?)";
        $artlist_id = $DB->insert_values($query_artlist, array($this->ebest_id, $art_id));
        varDump("RegisterArticle");
        varDump($artlist_id);
        array_push($this->artlist_ids,$artlist_id);
        return $artlist_id;
    }
    
    public function unregisterArticle($artlist_id){
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
		$lastEbest->clean_articles();
		return array('artikelnr' =>$lastEbest->getArtikel(), 'gesamtPreis'=>$lastEbest->getGesamtPreis());
	}
	
    public function clean_articles(){
        $this->getArtikellisten();
        $preis = 0.0;
        foreach ($this->artlist_ids as $i => $artikelliste){
            $artikel = new artikel($this->getArtikelausArtListID($artikelliste));
            $preis +=$artikel->preis();
        }
        $this->ebest_preis = $preis;
    }
    
    public function reset_artikel(){
        foreach ($this->artlist_ids as $i => $artikelliste){
            $this->unregisterArticle($artikelliste);
        }
        $this->artlist_ids = array();
        $this->ebest_preis = 0.0;
    }
    
    public function clear_articles(){
        foreach ($this->artlist_ids as $i => $artikelliste){
            $this->unregisterArticle($artikelliste);
        }
        $this->artlist_ids = array();
        $this->ebest_preis = 0.0;
        $this->saveAenderung();
    }

    public function preis_vermindern($preisVermindern){
        if (is_numeric($preisVermindern)){
            $this->ebest_preis -= $preisVermindern;
            return true;
        } else {
            return false;
        }
    }
    
    public function preis_erhoehen($preisDazu){
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
        $DB = new DB();
        $query = "UPDATE doener_einzelbestellung SET ebest_preis=? WHERE ebest_id = ".$this->ebest_id;
        $DB->update_values($query, array($this->ebest_preis));
        return $this->ebest_preis;
    }
}