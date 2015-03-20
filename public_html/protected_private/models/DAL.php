<?php

/*
 * Data access layer.
 */

class DAL {
    
    public function __construct(){}
    
    /* 
     * Connects to the database
     *
     * @author Patrice Boulet
     */
    private function dbconnect() {
        $conn = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD)
            or die ("Error in connection: " . pg_last_error());
        
        return $conn;
    }
    
    /*
     * Gets location id, address and name for all locations.
     * 
     * @author Patrice Boulet
     */
    public function get_all_restaurants($sorting){
     $sql = "SELECT l.location_id AS location_id, r._name AS name, l.street_address AS address, COUNT(*) as total_num_ratings, 
                    ROUND(AVG(g.price)::INTEGER) AS avg_price, ROUND(AVG(g.ambiance)::NUMERIC, 1) as avg_ambiance, 
                    ROUND(AVG(g.food)::NUMERIC, 1) as avg_food, ROUND(AVG(g.service)::NUMERIC, 1) as avg_service
                FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r, restaurant_ratings.rating g
                WHERE r.restaurant_id = l.restaurant_id AND g.location_id = l.location_id
                GROUP BY l.location_id, l.street_address, r._name";
    if($sorting !== null)
        $sql .= ' ORDER BY ' . $sorting;
        return $this->query($sql);
    }
    
    /*
     * Gets the cuisine types of this $location.
     *
     * @author Patrice Boulet
     */
    public function get_cuisine_types($location){
        
        $sql = "SELECT t._name as name
                FROM restaurant_ratings.restaurant_type t, restaurant_ratings.isOfType o, restaurant_ratings.locations l, restaurant_ratings.restaurant r
                WHERE l.restaurant_id = r.restaurant_id AND o.restaurant_id = r.restaurant_id AND t.type_id = o.type_id AND r._name = '" . $location . "'";
        return $this->query($sql);
    }
    
    /*
     * Gets location id, address and name for locations with types in $types.
     *
     * @author Patrice Boulet
     */
    public function get_only_restaurants_of_types($types_array, $sorting){
        
        $sql = "SELECT l.location_id AS location_id, r._name AS name, l.street_address AS address, COUNT(*) as total_num_ratings, 
                    ROUND(AVG(g.price)::INTEGER) AS avg_price, ROUND(AVG(g.ambiance)::NUMERIC, 1) as avg_ambiance, 
                    ROUND(AVG(g.food)::NUMERIC, 1) as avg_food, ROUND(AVG(g.service)::NUMERIC, 1) as avg_service
                FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r, restaurant_ratings.rating g, restaurant_ratings.isOfType t  
                WHERE r.restaurant_id = l.restaurant_id AND r.restaurant_id = t.restaurant_id 
                        AND g.location_id = l.location_id AND t.type_id IN (" . $this->get_user_specified_types_query($types_array) . ")
                GROUP BY l.location_id, l.street_address, r._name";
        if($sorting !== null)
            $sql .= ' ORDER BY ' . $sorting;
        return $this->query($sql);
    }
    
    /*
     * Returns a sql query that returns itself a type_id list
     * for user specified types in $types_array.
     *
     * @author Patrice Boulet
     */
    public function get_user_specified_types_query($types_array){
        // add escaped single quotes for sql syntax
        $escaped_types = array();
        foreach ($types_array as $t){
            array_push($escaped_types, "'" . $t . "'");
        }
        $types = join(',', $escaped_types);
        $sql = "SELECT t.type_id
                FROM restaurant_ratings.restaurant_type t
                WHERE t._name IN ($types)";
        return $sql;
    }
    
    
    /*
     * Get all the restaurant types for which there's at least one restaurant of this type
     * and the count of restaurant of this type.
     *
     * @author Patrice Boulet
     */
    public function get_restaurant_types_with_count(){
     $sql = "SELECT t._name AS _name, COUNT(*) AS _count
                FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r, restaurant_ratings.isOfType o,           restaurant_ratings.restaurant_type t
                WHERE l.restaurant_id = r.restaurant_id
                    AND o.restaurant_id = r.restaurant_id
                    AND o.type_id = t.type_id
                GROUP BY t._name";
        return $this->query($sql);
    }
    
    /*
     * Get all the restaurant types for which there's at least one restaurant of this type
     * and the count of restaurant of this type.
     *
     * @author Patrice Boulet
     */
    public function get_restaurant_types_with_count_as_RestaurantTypesWithCount(){
        // Reuse existing query
          $results = $this->get_restaurant_types_with_count();

          // check for results
          if (!$results){
            return $results;
          }
          else{
            // array to hold CarModel objects
            $object_results = array();
            
            for ($i = 0; $i < count($results); $i++) {
              $object_results[$i] = new RestaurantTypeWithCount($results[$i]);
            }
              
            // return array of CarModel objects
            return $object_results;
          }
    }
    
    
    /*
     * Gets every column of all the raters.
     *
     * @author Patrice Boulet
     */
    public function get_review_count_by_rater(){
     $sql = "SELECT u._name as _name, COUNT(*) as _count FROM restaurant_ratings.users u, restaurant_ratings.rater r, restaurant_ratings.rating s
                WHERE u.user_id=r.user_id AND r.rater_id = s.rater_id
                GROUP BY u._name";
        return $this->query($sql);
    }
    
    /* 
     * Prepares and executes a  generic query to the database
     * and then converts it to DALQueryResult object.
     *
     * @author Patrice Boulet
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
 *
 * @author Patrice Boulet
 */
class DALQueryResult {
    
    private $_results = array();
    
    public function __construct(){}
    
    public function set($var, $val) {
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

/*
 * Specific class object for restaurant types wit\
 * their count of locations.
 *
 * @author Patrice Boulet
 */

class RestaurantTypeWithCount {

    private $_name;
    private $_count;
    
    public function __construct (DALQueryResult $result){
        $this->_name = $result->_name;
        $this->_count = $result->_count;
    }
    public function __get($var){
        switch ($var){
          case 'name':
            return $this->_name;
            break;
          case 'count':
            return $this->_count;
            break;
        }
    }

    public function __toString(){
        return $this->_name;
    }
}

?>