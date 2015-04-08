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
     * Deletes a location, all the ratings related to it and its restaurant if there is no more 
     * location of this restaurant in the db.
     *
     * @author Patrice Boulet
     */
    public function delete_location($location_id){ 
        $sql = "WITH deleted AS (DELETE FROM restaurant_ratings.locations l
                    USING restaurant_ratings.restaurant r
                    WHERE r.restaurant_id = l.restaurant_id AND l.location_id = " . $location_id . "
                    RETURNING r.restaurant_id)
                    
                DELETE FROM restaurant_ratings.restaurant r2
                USING deleted, (SELECT r3.restaurant_id
                        FROM restaurant_ratings.restaurant r3, restaurant_ratings.locations l2
                        WHERE r3.restaurant_id = l2.restaurant_id
                        GROUP BY r3.restaurant_id
                        HAVING COUNT(*) < 2) AS locations_count
                WHERE r2.restaurant_id = deleted.restaurant_id AND 
                    r2.restaurant_id =  locations_count.restaurant_id AND 
                        r2.restaurant_id IN (locations_count.restaurant_id)";
        return $this->query($sql);
    } 
    
    
    /*
     * Checks login credentials.
     *
     * @author Patrice Boulet
     */
    public function check_credentials($username, $pswd){ 
        $sql = "SELECT u._name as name, u.pswd as pswd, r.rater_id as rater_id
                FROM restaurant_ratings.users u, restaurant_ratings.rater r
                WHERE u.user_id = r.user_id AND u._name = '" . $username . "' AND u.pswd = '" . $pswd . "';";
        return $this->query($sql);
    } 

    /*
     * Signup a new rater (also creates a new user associated with it).
     *
     * @author Patrice Boulet
     */
    public function signup_new_rater($email, $username, $pswd, $rater_type){ 
        $sql = "WITH user_insert AS (
                    INSERT INTO restaurant_ratings.users(email, _name, pswd, join_date)
                    VALUES ('" . $email . "', '" . $username . "', '" . $pswd . "', NOW()::DATE)
                    RETURNING user_id 
                )
                INSERT INTO restaurant_ratings.rater(user_id, _type)
                VALUES (  (SELECT i.user_id FROM user_insert i), '" . $rater_type . "');";
        return $this->query($sql);
    }   
    
    
    /*
     * Add a rating for a location.
     *
     * @author Patrice Boulet
     */
    public function add_new_location_rating($location_id, $rater_id, $price, $food, $ambiance, $service, $comments, $avg_rating){ 
        $sql = "INSERT INTO restaurant_ratings.rating(location_id, rater_id, date_written, price, food, 
                        ambiance, service, _comments, avg_rating)
                VALUES (" . $location_id . ", " .$rater_id . ", NOW()::DATE, " . $price . ", " .
                            $food . ", " . $ambiance . ", " . $service . ", '" . $comments . "', " .
                                $avg_rating . ");";
        return $this->query($sql);
    }   
    
    /*
     * Gets all rater types.
     *
     * @author Patrice Boulet
     */
    public function get_all_rater_types(){ 
        $sql = "SELECT r._type as type
                FROM restaurant_ratings.rater r
                GROUP BY _type";
        return $this->query($sql);
    }
    
    /*
     * Gets food menu items for this $location of type $type and category $category.
     *
     * @author Patrice Boulet
     */
    public function get_menu_items($location_id, $type, $category){
        
        $sql = "SELECT i._name, i.description, i.price
                FROM restaurant_ratings.locations l, restaurant_ratings.menu_item i
                WHERE l.location_id = i.location_id
                        AND i.location_id =" . $location_id .
                        "AND i._type ='" . $type . 
                        "' AND i.category = '" . $category . "'";
        return $this->query($sql);
    }
    
    /*
     * Gets food menu items for this $location of type $type and category $category.
     *
     * @author Patrice Boulet
     */
    public function get_beverages_categories($location_id){
        
        $sql = "SELECT i.category
                FROM restaurant_ratings.locations l, restaurant_ratings.menu_item i
                WHERE l.location_id = i.location_id AND i.location_id =" . $location_id . " AND i._type = 'drink'
                GROUP BY i.category";
        return $this->query($sql);
    }
    
    /*
     * Gets location and restaurant details for this $location.
     *
     * @author Patrice Boulet
     */
    public function get_location_details($location_id){
        
        $sql = "SELECT * 
                FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r
                WHERE l.restaurant_id = r.restaurant_id AND l.location_id =" . $location_id;
        return $this->query($sql);
    }
    
    /*
     * Gets rating, location, and restaurant details for this $location.
     *
     * @author Junyi Dai
     */
    public function get_location_ratings($location_id){
        
        $sql = "SELECT *
                FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r, restaurant_ratings.rating ra,        restaurant_ratings.rater rat, restaurant_ratings.users u
                WHERE r.restaurant_id = l.restaurant_id AND l.location_id = ra.location_id 
                        AND ra.rater_id = rat.rater_id AND rat.user_id = u.user_id 
                        AND l.location_id =" . $location_id;
        return $this->query($sql);
    }
    
    /*
     * Gets location id, address and name for all locations.
     * 
     * @author Patrice Boulet
     */
    public function get_all_restaurants($sorting){
     $sql = "SELECT l.location_id AS location_id, r._name AS name, l.street_address AS address, COUNT(*) as total_num_ratings, 
                    ROUND(AVG(g.price)::INTEGER) AS avg_price, ROUND(AVG(g.ambiance)::NUMERIC, 1) as avg_ambiance, 
                    ROUND(AVG(g.food)::NUMERIC, 1) as avg_food, ROUND(AVG(g.service)::NUMERIC, 1) as avg_service,
                    ROUND(AVG(g.avg_rating)::NUMERIC, 1) as avg_rating, MIN(date_part('days', now() - g.date_written)) as days_written_to_now,
                    ROUND((SUM((extract('epoch' from g.date_written)/100000000)*g.avg_rating)/COUNT(*))::NUMERIC, 1) as popularity
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
                    ROUND(AVG(g.food)::NUMERIC, 1) as avg_food, ROUND(AVG(g.service)::NUMERIC, 1) as avg_service,
                    ROUND(AVG(g.avg_rating)::NUMERIC, 1) as avg_rating, MIN(date_part('days', now() - g.date_written)) as days_written_to_now,
                    ROUND((SUM((extract('epoch' from g.date_written)/100000000)*g.avg_rating)/COUNT(*))::NUMERIC, 1) as popularity
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
     * Prepares and executes a  generic query to the database
     * and then converts it to DALQueryResult object.
     *
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