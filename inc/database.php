<?php


class DB {
    
	public static $debug = false;
	
    private function connect(){
        $host_name  = strpos(__url,"localhost") ? '127.0.0.1' : "db528830179.db.1and1.com";
        $database   = strpos(__url,"localhost") ? "doenerbank" : "db528830179"  ;
        $user_name  = strpos(__url,"localhost") ? "doenermann" : "dbo528830179";
        $password   = strpos(__url,"localhost") ? "all4theD03N3r" : "";
		
        try {
            $connect = new PDO("mysql:dbname=".$database.";hostname=".$host_name.";charset=utf8", $user_name, $password);
        } catch (PDOException $e) {
            if(DB::$debug === true ){var_dump($e->getMessage());}
        }
        return $connect;
    }
    
    public function query($query){
        $conn = $this->connect();
        try{
            $prep = $conn->prepare($query);
            $prep->execute(array());
			if(DB::$debug === true ){var_dump($this->showQuery($query,array()));}
            $result = $prep->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if(DB::$debug === true ){var_dump($e->getMessage());}
        }
            if(empty($result)){return false;}
            return $result;
    }
    public function query_values($query,$value_array){
        $conn = $this->connect();
        try{
            $prep = $conn->prepare($query);
			//if(DB::$debug === true ){var_dump($this->showQuery($query,$value_array));}
			$prep->execute($value_array);
            $result = $prep->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if(DB::$debug === true ){var_dump($e->getMessage());}
        }
        if(empty($result)){return false;}
        return $result;
    }
    
    public function insert($query){
        $conn = $this->connect();
        $lastID = false;
        try{
            $delta_rows = $conn->exec($query);
            if($delta_rows === false){return false;}
            $lastID = $conn->lastInsertId();
        } catch (PDOException $e) {
            if(DB::$debug === true ){var_dump($e->getMessage());}
        }
        return $lastID;
    }
    
    public function insert_values($query,$values){
        $conn = $this->connect();
        $lastID = false;
        foreach($values as $i=>$value){
            if(gettype($value) === 'string'){
                $query = preg_replace("/[?]/","'".$value."'",$query,1);
            } else {
                $query = preg_replace("/[?]/",$value,$query,1);
            }
        }
		//if(DB::$debug === true ){var_dump($query);}
        try{
            $delta_rows = $conn->exec($query);
            if($delta_rows === false){return false;}
            $lastID = $conn->lastInsertId();
        } catch (PDOException $e) {
            if(DB::$debug === true ){var_dump($e->getMessage());}
        }
        
        return $lastID;
    }
    
        public function update($query){
        $conn = $this->connect();
        try{
            $delta_rows = $conn->exec($query);
            if($delta_rows === false){return false;}
        } catch (PDOException $e) {
            if(DB::$debug === true ){var_dump($e->getMessage());}
        }
        return $delta_rows;
    }
    
    public function update_values($query,$values){
        $conn = $this->connect();
        foreach($values as $value){
            if(gettype($value) === 'string'){
                $query = preg_replace("/[?]/","'".$value."'",$query,1);
            } else {
                $query = preg_replace("/[?]/",$value,$query,1);
            }
        }
		 if(DB::$debug === true ){var_dump($query);}
        try{
            $delta_rows = $conn->exec($query);
            if($delta_rows === false){return false;}
        } catch (PDOException $e) {
            if(DB::$debug === true ){var_dump($e->getMessage());}
        }
        
        return $delta_rows;
    }
    
    public static function crypt($unencrypted_pass){
        return sha1($unencrypted_pass);
    }
	
	public function showQuery($query, $params)
    {
        $keys = array();
        $values = array();
        
        # build a regular expression for each parameter
        foreach ($params as $key=>$value)
        {
            if (is_string($key))
            {
                $keys[] = '/:'.$key.'/';
            }
            else
            {
                $keys[] = '/[?]/';
            }
            
            if(is_numeric($value))
            {
                $values[] = intval($value);
            }
            else
            {
                $values[] = '"'.$value .'"';
            }
        }
        
        $query = preg_replace($keys, $values, $query, 1, $count);
        return $query;
    }
	
}
