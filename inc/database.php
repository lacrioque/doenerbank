<?php


class DB {
    
    private function connect(){
        $host_name  = "db528830179.db.1and1.com";
        $database   = "db528830179";
        $user_name  = "dbo528830179";
        $password   = "";

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
            $prep->execute();
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
            $prep = $conn->prepare($query,$value_array);
            $prep->execute();
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
        foreach($values as $value){
            if(gettype($value) === 'string'){
                $query = str_replace("?","'".$value."'",1);
            } else {
                $query = str_replace("?",$value,1);
            }
        }
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
                $query = str_replace("?","'".$value."'",1);
            } else {
                $query = str_replace("?",$value,1);
            }
        }
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
}
