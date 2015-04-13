<?php

/*
 * Data access layer.
 */

class DAL {
    
    
    //////////////////////EXTRA QUERIES
    
    /** Folowing queries @author Quasim */
    /* Query N ----------
    set search_path = 'restaurant_ratings';

    --all ratings smaller than max
    Select DISTINCT usersOut._name, usersOut.email, compMax
    from rating,rater, users usersOut,(
                --gets max rating from a rater
                select max(avg_rating)
                from rater, users, rating
                where rater.rater_id = rating.rater_id AND rater.user_id = users.user_id AND users._name = 'John'
                )as Compmax
    where avg_rating < max AND rating.rater_id = rater.rater_id AND rater.user_id = usersOut.user_id

    */

    /* QUERY K
    set search_path = 'restaurant_ratings';

        select t.avg_rating, t._name, t.join_date, rater_reputation from(
                select DISTINCT  TotalNumberOfRating.*,tmp.avg_rating, tmp._name, tmp.join_date, ((tmp.found_helpful * (tmp.found_helpful + tmp.wasnt_helpful)) - tmp.wasnt_helpful * (tmp.found_helpful + tmp.wasnt_helpful)) AS rater_reputation 
                from
                (
                select rater.*, rating.avg_rating, users._name, users.join_date
                from rater, rating, users
                where rater.rater_id = rating.rater_id AND users.user_id = rater.user_id
                ) as tmp,
                (
                select max(avg_rating) from(
                select DISTINCT  tmp.avg_rating, tmp._name, tmp.join_date, ((tmp.found_helpful * (tmp.found_helpful + tmp.wasnt_helpful)) - tmp.wasnt_helpful * (tmp.found_helpful + tmp.wasnt_helpful)) AS rater_reputation from(
                select rater.*, rating.avg_rating, users._name, users.join_date
                from rater, rating, users
                where rater.rater_id = rating.rater_id AND users.user_id = rater.user_id
                )as tmp
                )t2) as TotalNumberOfRating
                ) as t
                where avg_rating = max 
            --Where tmp.avg_rating = max(tmp.avg_rating)

            */
    
    /* @author Jeff
    -- Query (c)
    SELECT rt._name, r._name, l.manager_name, l.first_open_date
    FROM locations l, isoftype isa, restaurant_type rt, restaurant r
    WHERE l.restaurant_id = isa.restaurant_id AND isa.type_id = rt.type_id AND  r.restaurant_id = l.restaurant_id
        AND rt._name = 'Asian'

    -- Query (g)
    SELECT r._name, l.phone_number, rt._name, ra.date_written
    FROM restaurant r, locations l, rating ra, isoftype isa, restaurant_type rt
    WHERE r.restaurant_id = l.restaurant_id AND ra.location_id = l.location_id
        AND isa.restaurant_id = r.restaurant_id AND isa.type_id = rt.type_id
        AND NOT (EXTRACT(MONTH FROM ra.date_written) = '1')

    -- Query (h)
    SELECT r._name, l.first_open_date, ra.date_written, ra.service
    FROM restaurant r, rating ra, locations l
    WHERE r.restaurant_id = l.location_id AND l.location_id = ra.location_id
        AND ra.service < (
                    SELECT MAX(ra.service)
                    FROM rating ra, rater r
                    WHERE ra.rater_id = r.rater_id AND r.rater_id = 3
                )
    ORDER BY ra.date_written



        */
    
    
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
     * Gets all rater details.
     *
     * @author Junyi Dai
     */
    public function get_all_raters($sorting){ 

          if($sorting == null)
                $sorting = 'u._name';
            
        $sql = "SELECT rat.*, u._name, u.email, u.pswd, u.join_date, (
                    SELECT COUNT(*)
                    FROM(   
                            SELECT *
                            FROM restaurant_ratings.rating ra
                            WHERE rat.rater_id = ra.rater_id) AS tmp
                        ) AS total_num_ratings, ((rat.found_helpful * (rat.found_helpful + rat.wasnt_helpful)) - rat.wasnt_helpful * (rat.found_helpful + rat.wasnt_helpful)) AS rater_reputation
                FROM restaurant_ratings.rater rat, restaurant_ratings.users u
                WHERE rat.user_id = u.user_id " .
                "ORDER BY " . $sorting ;
        return $this->query($sql);
    }
	
    /*
     * Deletes menu item.
     *
     * @author Patrice Boulet
     */
    public function delete_menu_item($item_id){ 
        $sql = "DELETE FROM restaurant_ratings.menu_item i
                WHERE i.item_id = " . $item_id;
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
     * Updates found_helpful and wasn't helpful of rating and rater.
     *
     * @author Patrice Boulet
     */
    public function update_helpfulness($helpful, $rating_id, $rater_id){ 
        if( $helpful === 'true'){
            
            $sql1 = "UPDATE restaurant_ratings.rating SET found_helpful = found_helpful + 1
                    WHERE rating_id = " . $rating_id;
            
            $sql2 = "UPDATE restaurant_ratings.rater SET found_helpful = found_helpful + 1
                    WHERE rater_id = " . $rater_id;
        }else{
            $sql1 = "UPDATE restaurant_ratings.rating SET wasnt_helpful = wasnt_helpful + 1
                    WHERE rating_id = " . $rating_id;

            $sql2 = "UPDATE restaurant_ratings.rater SET wasnt_helpful = wasnt_helpful + 1
                    WHERE rater_id = " . $rater_id;
        }
        
        
            $dbh = $this->dbconnect();
            $stmt = pg_prepare($dbh, "ps1", $sql1);
            $stmt = pg_prepare($dbh, "ps2", $sql2);
            $res1 = pg_execute($dbh, "ps1", array());
            $res2 = pg_execute($dbh, "ps2", array());
            
            //free memory
            pg_free_result($res1);
            pg_free_result($res2);
            //close connection
            pg_close($dbh);
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
            RETURNING user_id )
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
        $sql = "WITH tmp AS (INSERT INTO restaurant_ratings.rating(location_id, rater_id, date_written, price, food, 
                        ambiance, service, _comments, avg_rating)
                VALUES (" . $location_id . ", " .$rater_id . ", NOW()::DATE, " . $price . ", " .
                            $food . ", " . $ambiance . ", " . $service . ", '" . $comments . "', " .
                                $avg_rating . ")
                RETURNING rating_id)
                SELECT rating_id
                FROM tmp";
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
     * Gets all restaurants and their ids.
     *
     * @author Patrice Boulet
     */
    public function get_all_restaurants(){ 
        $sql = "    SELECT r.restaurant_id, _name AS name
                    FROM restaurant_ratings.restaurant r";
        return $this->query_with_stmt_name($sql, "restaurants");
    }
    
    /*
     * Adds a restaurant.
     *
     * @author Patrice Boulet
     */
    public function add_restaurant($restaurantName, $restaurantURL, $typesArray){        
        
        $sql = "WITH new_restaurant AS (INSERT INTO restaurant_ratings.restaurant(_name, url)
                                        VALUES ('" . $restaurantName . "', '" . $restaurantURL . "')
                                        RETURNING restaurant_id)
                                        
                INSERT INTO restaurant_ratings.isoftype(type_id, restaurant_id) ";
        
                $first_type = true;
                foreach($typesArray as $type){
                    if($first_type){
                        $sql .= "VALUES (" . $type . ", (SELECT r.restaurant_id FROM new_restaurant r))";
                        $first_type = false;
                    }else{
                        $sql .= ", (" . $type . ", (SELECT r.restaurant_id FROM new_restaurant r))";
                    }
                }
        
                $sql .= " RETURNING (SELECT r.restaurant_id FROM new_restaurant r)";
        return $this->query($sql);
    }
    
    /*
     * Adds a location.
     *
     * @author Patrice Boulet
     */
    public function add_location($openingDate, $manager, $phone, $address, $openingHours, $restaurant_id){        
        
        $sql = "INSERT INTO restaurant_ratings.locations(
                        first_open_date, manager_name, phone_number, street_address, 
                            opening_hours, restaurant_id)
                VALUES ('" . $openingDate . "', '" . $manager . "', '" . $phone . "', '" . $address . "', '" .
                                $openingHours . "', " . $restaurant_id . ")";
        return $this->query_with_stmt_name($sql, "add_location");
    }
        
    /*
     * Add a menu item to a rating. 
     *
     * Note: I'm not using
     * the query method here because the prepared statements
     * names overlapse so I have to give them a different name each time
     * it executes.
     *
     * @author Patrice Boulet
     */
    public function add_rating_item_no_rating($item_id, $rating_id){ 
        
        $dbh = $this->dbconnect();
        
        $sql = "INSERT INTO restaurant_ratings.rating_item(item_id, rating_id)
                VALUES (" . $item_id . ", " . $rating_id . ")";
        
        $stmt = pg_prepare($dbh, $item_id, $sql);

        $res = pg_execute($dbh, $item_id, array());
        //free memory
        pg_free_result($res);
        //close connection
        pg_close($dbh);
    }

    /*
     * Gets food menu items for this $location of type $type and category $category.
     *
     * @author Patrice Boulet
     */
    public function get_menu_items($location_id, $type, $category){
        
        $sql = "SELECT i.item_id, i._name, i.description, i.price
        FROM restaurant_ratings.locations l, restaurant_ratings.menu_item i
        WHERE l.location_id = i.location_id
        AND i.location_id =" . $location_id .
        "AND i._type ='" . $type . "'";
        
        if($category != null)
            $sql .= " AND i.category = '" . $category . "'";
        
        return $this->query($sql);
    }
    
    /*
     * Gets all beverages categories available for this location.
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
     * Gets all categories available for this $type.
     *
     * @author Patrice Boulet
     */
    public function get_categories_by_type($type){
        
        $sql = "SELECT i.category
                FROM restaurant_ratings.menu_item i
                WHERE i._type = '" . $type . "'
                GROUP BY i.category";
        return $this->query($sql);
    }
    
        
    /*
     * Adds a menu item to that $location.
     *
     * @author Patrice Boulet
     */
    public function add_menu_item($location_id, $name, $type, $category, $description, $price){
        
        $sql = "INSERT INTO restaurant_ratings.menu_item(_name, _type, category, description, price, location_id)
                VALUES ('". $name ."', '". $type ."', '". $category ."', '". $description ."', ". $price .", ". $location_id .");";
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
     * Takes a location Id as a parameter. 
     * For that location Id this method will return the location that corresponds to that ID.
     * This method will also return the ratings associated to that location, and the details associated with that location,
     * such as hours and ect.
     * 
     *
     * @author Patrice Boulet, Junyi Dai, Qasim Ahmed
     */
    public function get_location_ratings($location_id, $sorting){
        
        $sql = "WITH 
        number_of_ratings_of_this_loc_per_rater AS (SELECT ra.rater_id, COUNT(*) as rater_ratings_for_this_loc
        FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r, restaurant_ratings.rating ra, restaurant_ratings.rater rat,               restaurant_ratings.users u
        WHERE r.restaurant_id = l.restaurant_id AND l.location_id = ra.location_id 
		  AND ra.rater_id = rat.rater_id AND rat.user_id = u.user_id 
		  AND l.location_id =" . $location_id . "
        GROUP BY ra.rater_id),

        number_of_total_ratings_per_rater AS (SELECT ra.rater_id, COUNT(*) as total_rater_ratings
        FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r, restaurant_ratings.rating ra, restaurant_ratings.rater rat,               restaurant_ratings.users u
        WHERE r.restaurant_id = l.restaurant_id AND l.location_id = ra.location_id 
		  AND ra.rater_id = rat.rater_id AND rat.user_id = u.user_id 
        GROUP BY ra.rater_id)

        SELECT *, ((rat.found_helpful * (rat.found_helpful + rat.wasnt_helpful)) - rat.wasnt_helpful * (rat.found_helpful + rat.wasnt_helpful)) AS rater_reputation
        FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r, 
		  restaurant_ratings.rating ra, restaurant_ratings.rater rat, restaurant_ratings.users u,
		  number_of_ratings_of_this_loc_per_rater, number_of_total_ratings_per_rater
        WHERE r.restaurant_id = l.restaurant_id AND l.location_id = ra.location_id 
		  AND ra.rater_id = rat.rater_id AND rat.user_id = u.user_id 
		  AND ra.rater_id = number_of_ratings_of_this_loc_per_rater.rater_id 
		  AND ra.rater_id = number_of_total_ratings_per_rater.rater_id AND l.location_id = " . $location_id;
        if($sorting !== null)
            $sql .= ' ORDER BY ' .$sorting;

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
     * Gets the rating items name and price for this rating.
     *
     * @author Patrice Boulet
     */
    public function get_rating_items_for_rating($rating_id){
        
        $sql = "SELECT DISTINCT ON (i.rating_item_id) _name, m.price
                FROM restaurant_ratings.rating r, restaurant_ratings.rating_item i, restaurant_ratings.menu_item m
                WHERE r.rating_id = i.rating_id AND i.item_id = m.item_id AND r.rating_id = " . $rating_id;
        return $this->query($sql);
    }
    
    /*
     * Gets location id, address and name for locations with types in $types.
     *
     * @author Patrice Boulet
     */
    public function get_locations_list($types_array, $sorting, $rating_filters){
        $first_where_clause = true;
        
        $sql = "";

        $global_r_filters = $rating_filters[0];
        $food_r_filters = $rating_filters[1];
        $service_r_filters = $rating_filters[2];
        $ambiance_r_filters = $rating_filters[3];
        
        if ( count($global_r_filters) > 0 || count($food_r_filters) > 0 || count($service_r_filters) > 0 || count($ambiance_r_filters) > 0){
            $sql .= "WITH location_tbl AS (";
        }
        
        $sql .= "SELECT second_tbl.location_id, second_tbl.name, second_tbl.address , COUNT(*) as total_num_ratings, 
                    ROUND(AVG(second_tbl.price)::INTEGER) AS avg_price, ROUND(AVG(second_tbl.ambiance)::NUMERIC, 1) as avg_ambiance, 
                    ROUND(AVG(second_tbl.food)::NUMERIC, 1) as avg_food, ROUND(AVG(second_tbl.service)::NUMERIC, 1) as avg_service,
                    ROUND(AVG(second_tbl.avg_rating)::NUMERIC, 1) as avg_rating, MIN(date_part('days', now() - second_tbl.date_written)) as                     days_written_to_now,
                    ROUND((SUM((extract('epoch' from second_tbl.date_written)/100000000)*second_tbl.avg_rating)/COUNT(*))::NUMERIC, 1) as                       popularity
                FROM (
                    SELECT DISTINCT ON (first_tbl.rating_id) first_tbl.* 
                    FROM
                        (SELECT l.location_id AS location_id, r._name AS name, l.street_address AS address,
                            g.price, g.food, g.service, g.ambiance, g.avg_rating, g.date_written, g.rating_id
                        FROM 
				restaurant_ratings.locations l JOIN restaurant_ratings.restaurant r ON r.restaurant_id = l.restaurant_id 
				JOIN restaurant_ratings.isOfType t ON r.restaurant_id = t.restaurant_id 
				LEFT JOIN restaurant_ratings.rating g ON g.location_id = l.location_id ";
                        
        if($types_array !== null){            
            $sql .= " AND t.type_id IN (" . $this->get_user_specified_types_query($types_array) . ")";
        }
        
        $sql .= "
                        ) AS first_tbl
                    ) AS second_tbl

                GROUP BY second_tbl.location_id, second_tbl.address, second_tbl.name";
        
        if ( $global_r_filters !== null || $food_r_filters !== null ||
                    $service_r_filters !== null || $ambiance_r_filters !== null){
            $first_filter = true;
            
            $sql.= ")   SELECT * 
                        FROM location_tbl ";
            
            if( count($global_r_filters) > 0){
                if($first_where_clause){
                     $sql.= "WHERE ";
                    $first_where_clause = false;
                }
                $sql.= "floor(avg_rating) IN (" . join(',', $global_r_filters) . ")";
                $first_filter = false;
            }
            
            if( count($food_r_filters) > 0){
                if($first_where_clause){
                     $sql.= "WHERE ";
                    $first_where_clause = false;
                }
                if( !$first_filter )
                    $sql.= " AND ";
                $sql.= "floor(avg_food) IN (" . join(',', $food_r_filters) . ")";
                $first_filter = false;
            }
            
            if( count($service_r_filters) > 0){
                if($first_where_clause){
                     $sql.= "WHERE ";
                    $first_where_clause = false;
                }
                if( !$first_filter )
                    $sql.= " AND ";
                $sql.= "floor(avg_service) IN (" . join(',', $service_r_filters) . ")";
                $first_filter = false;
            }
            
            if( count($ambiance_r_filters) > 0){
                if($first_where_clause){
                     $sql.= "WHERE ";
                    $first_where_clause = false;
                }
                if( !$first_filter )
                    $sql.= " AND ";
                $sql.= "floor(avg_ambiance) IN (" . join(',', $ambiance_r_filters) . ")";
                $first_filter = false;
            }
        }
        
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
     * Get all the restaurant types.
     *
     * @author Patrice Boulet
     */
    public function get_restaurant_types(){
    
       $sql = "SELECT t.type_id, t._name
FROM restaurant_ratings.restaurant_type t";
        
    return $this->query_with_stmt_name($sql, "types");
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
     * Gets all rater details.
     *
     * @author Qasim Ahmed
     */
    public function getRaterInformation($username){ 
        $sql = "SELECT rat.*, u._name, u.email, u.pswd, u.join_date, (
                    SELECT COUNT(*)
                    FROM(   
                            SELECT *
                            FROM restaurant_ratings.rating ra
                            WHERE rat.rater_id = ra.rater_id) AS tmp
                        ) AS total_num_ratings, ((rat.found_helpful * (rat.found_helpful + rat.wasnt_helpful)) - rat.wasnt_helpful * (rat.found_helpful + rat.wasnt_helpful)) AS rater_reputation
                FROM restaurant_ratings.rater rat, restaurant_ratings.users u
                WHERE rat.user_id = u.user_id AND u._name= '" . $username . "';";
        return $this->query($sql);
    }

	   /*
     *  Gets all the ratings of a particular rater
     *  @author Qasim Ahmed
     */
    public function getRaterRatings($rater_id, $sorting_type){
        if($rater_id == null)
            $rater_id = 1;
        if($sorting_type == null)
            $sorting_type = 'date_written DESC';
        $sql = "SELECT * 
                FROM restaurant_ratings.rater rater, restaurant_ratings.rating rating, restaurant_ratings.locations l, restaurant_ratings.restaurant rest
                WHERE rater.rater_id = rating.rater_id AND l.location_id = rating.location_id AND rest.restaurant_id = l.restaurant_id AND rater.rater_id=" . $rater_id .
                " ORDER BY " . $sorting_type;

        return $this->query($sql);
    }
    /*
     *  Deletes a rater from the database
     *  @author Qasim Ahmed
     */
    public function deleteRater($rater_id){
        $sql =" DELETE
                FROM restaurant_ratings.rater rater
                WHERE rater.rater_id =" . $rater_id;

        return $this->query($sql);
    }

    /*
     *  Finds the number of ratings from a particular rater to a particular restaurant
     *  @author Qasim Ahmed
     */
    public function numRatingsFromRaterForLocation($location_id, $rater_id){
        $sql ="SELECT l.location_id, ra.rater_id, COUNT(*) as rater_ratings_for_this_loc
                FROM restaurant_ratings.locations l, restaurant_ratings.restaurant r, restaurant_ratings.rating ra, restaurant_ratings.rater rat,               restaurant_ratings.users u
                WHERE r.restaurant_id = l.restaurant_id AND l.location_id = ra.location_id 
                  AND ra.rater_id = rat.rater_id AND rat.user_id = u.user_id 
                  AND l.location_id = " . $location_id . "AND rat.rater_id = " . $rater_id .
                "GROUP BY ra.rater_id, l.location_id";

        return $this->query($sql);
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
    private function query_with_stmt_name($sql, $stmtid){
        $dbh = $this->dbconnect();
        
        $stmt = pg_prepare($dbh, $stmtid, $sql);

        $res = pg_execute($dbh, $stmtid, array());
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