<?php

class einzelbestellung {
    private $ebest_id;
    private $ebest_preis;
    private $artlist_ids = array();
    
    public function __construct($best_id, $user_id){
        $DB = new DB();
        $this->ebest_preis = 0.0;
        $query = "INSERT INTO doener_einzelbestellung (user_id,best_id,ebest_preis) VALUES(?,?,?)";
        $preEbest_id = $DB->insert_values($query, array($user_id, $ebest_id, $this->ebest_preis));
        $this->ebest_id = $preEbest_id[0];
    }
    
    public function registerArticle($art_id){
        $DB = new DB();
        $artikel = new Artikel($art_id);
        $this->gesamtpreis_erhoehen($artikel->preis());
        $query_artlist = "INSERT INTO doener_artikelliste (ebest_id, art_id) VALUES(?, ?)";
        $artlist_id = $DB->insert_values($query_artlist, array($this->ebest_id, $art_id));
        $this->$artlist_ids[]=$artlist_id[0];
        return $artlist_id[0];
    }
    
    public function getArtikelausArtListID($artlist_id){
        $DB = new DB();
        $query = "SELECT art_id FROM doener_artikelliste WHERE artlist_id = ?";
        $artikel = $DB->query_values($query, array($artlist_id));
        return new Artikel($artikel[0]);
    }

}