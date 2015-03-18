<?php

/*
 * Data access layer.
 */

class DAL {
    
    public function __construct(){}
    
    
    /* 
     * Connects to the database
     */
    private function dbconnect() {
        $conn = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD)
            or die ("Error in connection: " . pg_last_error());
        
        return $conn;
    }
    
    /*
     * Gets every column of all the locations.
     */
    public function get_all_restaurants(){
     $sql = "SELECT l.street_address AS address, r._name AS name 
                FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r    
                WHERE r.restaurant_id = l.restaurant_id";
        return $this->query($sql);
    }
    
    /*
     * Get all the restaurant types for which there's at least one restaurant of this type
     * and the count of restaurant of this type.
     */
    public function get_restaurant_types_with_count(){
     $sql = "SELECT t._name AS name, COUNT(*) AS count
                FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r, restaurant_ratings.isOfType o,           restaurant_ratings.restaurant_type t
                WHERE l.restaurant_id = r.restaurant_id
                    AND o.restaurant_id = r.restaurant_id
                    AND o.type_id = t.type_id
                GROUP BY t._name";
        return $this->query($sql);
    }
    
    
    /*
     * Gets every column of all the raters.
     */
    public function get_review_count_by_rater(){
     $sql = "SELECT u._name as name, COUNT(*) as count FROM restaurant_ratings.users u, restaurant_ratings.rater r, restaurant_ratings.rating s
                WHERE u.user_id=r.user_id AND r.rater_id = s.rater_id
                GROUP BY u._name";
        return $this->query($sql);
    }
    
    /* 
     * Prepares and executes a  generic query to the database
     * and then converts it to DALQueryResult object.
     * 
     * return
     *      If there are not any results, it returns null on a SELECT query, 
     * false on other queries. If the query was successful and the query was 
     * not a SELECT query, it will return true. 
     */  
    private function query($sql){
        $dbh = $this->dbconnect();
        
        $stmt = pg_prepare($dbh, "ps", $sql);

        $res = pg_execute($dbh, "ps", array());
        if (!$res) {
            if( strpos($sql, 'SELECT') === false){
                return false;
            }
            else{
                return null;
            }
        }else{
            if(strpos($sql, 'SELECT') === false){
                return true;
            }
        }
        
        $results = array();
        
        while($row=pg_fetch_array($res)){
            $result = new DALQueryResult();
            
            foreach ($row as $k=>$v){
                $result->$k = $v;
            }
            
            $results[] = $result;
        }
        
        
        //free memory
        pg_free_result($res);
        //close connection
        pg_close($dbh);
        
        return $results;
    }
}

/* 
 * Generic database query result.
 */
class DALQueryResult {
    
    private $_results = array();
    
    public function __construct(){}
    
    public function __set($var, $val) {
        $this->_results[$var] = $val;
    }
    
    public function __get($var) {
        if(isset($this->_results[$var])){
            return $this->_results[$var];
        }else{
            return null;
        }
    }
}

?>