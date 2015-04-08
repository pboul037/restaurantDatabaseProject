<?php
        session_start();
        // set session to expire in 30 minutes

        //Retrieve login id from session object
        //if(!isset($_SESSION['loginid'])){
        //    echo "Please" . "<a href='Login.php'>Login</a>";
        //    exit;
        //}
        
        // include configuration
        require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');

        $dal =  new DAL();

        $locations_list;
        $restaurant_types;
        $each_location_types = array();    
        $selected_cuisine_types = array();

        $types_new_options = ""; 
        $type_tags = "";
        $sorting_tag = "";
        $new_locations_list = "";
        

        if (isset($_POST['delete_location']))
            $dal->delete_location($_POST['delete_location']);
            
        get_locations_list();
        get_locations_types();


        /*
         * Get cuisine types for each location in the list and put them a session variable.
         *
         * @author Patrice Boulet
         */
        function get_locations_types () {
            global $dal, $locations_list;
            
            foreach($locations_list as $location){
                if( !isset($_SESSION[$location->name . '-types']) || count($_SESSION[$location->name . '-types']) == 0){
                    $location_types_array = array();
                    
                    foreach($dal->get_cuisine_types($location->name) as $location_type){
                        array_push($location_types_array, $location_type->name);
                    }
                    
                    $_SESSION[$location->name . '-types'] = $location_types_array;
                }
            }
        }


        /*
         * Gets the locations list and updates all search html elements.
         * 
         * @author Patrice Boulet
         */
        function get_locations_list(){
            
            global $dal, $locations_list, $restaurant_types, $types_new_options, $type_tags, $sorting_tag, $new_locations_list;
           
            $type_selected = isset($_POST['type_selected']);
            $sorting_selected = isset($_POST['sorting_selected']);
            $clear_search_options = isset($_POST['clear_all_search_options']);
            $delete_selected = isset($_POST['delete_location']);
            
            if($sorting_selected)
                $_SESSION['locations_sorting_selected'] = $_POST['sorting_selected'];
            
            // send sorting criterias if any
            if( isset($_SESSION['locations_sorting_selected']) && !$clear_search_options)
                $locations_list = $dal->get_all_restaurants($_SESSION['locations_sorting_selected']);
            else
                $locations_list = $dal->get_all_restaurants(null);
            
            $restaurant_types = $dal->get_restaurant_types_with_count_as_RestaurantTypesWithCount();
            
            // initial load - no user specified search options
            if (!isset($_SESSION['restaurant_types_selected']))
                    $_SESSION['restaurant_types_selected'] =  array();        
            
            if( !$type_selected && !isset($_SESSION['locations_sorting_selected']) && count($_SESSION['restaurant_types_selected']) == 0 ){
                if( $delete_selected){
                    $response = array();
                    $disableClearSearchButton = true;
                    array_push($response, null, null, get_location_html_items($locations_list), null, true);
                    echo json_encode($response);
                }
                return;
                
            }else{
                if($clear_search_options){
                    unset($_SESSION['restaurant_types_selected']);
                    unset($_SESSION['locations_sorting_selected']);
                    $_SESSION['restaurant_types_selected'] = array();
                }
                
                if ($type_selected) {
                    $selectedvalue = $_POST['type_selected'];            
                    $type = explode('(', $selectedvalue)[0];

                    for ($i = 0; $i < count($restaurant_types); $i++) { 
                      if( strcmp($type,$restaurant_types[$i]->name) == 0){
                        array_push($_SESSION['restaurant_types_selected'], $restaurant_types[$i]->name); 
                      }
                    }
                }
                
                // initialize response
                if($type_selected || $clear_search_options || $sorting_selected){
                    
                    $response = array();
                
                    if(!$sorting_selected){
                        // prepare the new options 
                        $types_new_options .= '<select id="types_select" class="btn btn-default dropdown-toggle"                                                                onchange="updateSelectedTypes(this.value)"><option value="" disabled selected>Show only type(s)...</option>'; 
                    }
                }
                
                if(!$sorting_selected){
                    for ($i = 0; $i < count($restaurant_types); $i++) {
                        $found = false;
                        foreach($_SESSION['restaurant_types_selected'] as $already_selected){ 
                            if ( $restaurant_types[$i]->name == $already_selected ){
                                unset($restaurant_types[$i]);
                                $restaurant_types = array_values($restaurant_types);
                                $found = true;
                            } 
                        } 
                        if($found == false && $type_selected || $clear_search_options){
                            $types_new_options .= "<option value='" . $restaurant_types[$i]->name . "'>" . $restaurant_types[$i]->name . ' (' .                                                           $restaurant_types[$i]->count . ')</option>';                                       
                        }
                    }
                }
                
                if($type_selected || $clear_search_options)
                    $types_new_options .= '</select>';
                
                if( count($_SESSION['restaurant_types_selected']) !== 0 || $clear_search_options || isset($_SESSION['locations_sorting_selected'])){
                    
                    if(!$clear_search_options && count($_SESSION['restaurant_types_selected']) !== 0 ){
                        $sorting_param = isset($_SESSION['locations_sorting_selected']) ? $_SESSION['locations_sorting_selected'] : null;
                        $restaurant_only_of_types = $dal->get_only_restaurants_of_types($_SESSION['restaurant_types_selected'], $sorting_param);
                    }
                    
                    if( isset($_SESSION['locations_sorting_selected'])){
                        $sorting_tag .= get_selected_sorting_tag_html_item();
                    }
                    
                    if($type_selected || count($_SESSION['restaurant_types_selected']) !== 0 && $sorting_selected){
                        foreach($_SESSION['restaurant_types_selected'] as $already_selected){
                                $type_tags .= get_cloud_cuisine_tag_html_item($already_selected);
                            }
                        $new_locations_list .= get_location_html_items($restaurant_only_of_types);
                    }
                    
                    if($clear_search_options || count($_SESSION['restaurant_types_selected']) == 0){
                        $new_locations_list .= get_location_html_items($locations_list);
                    }
                    
                    if($type_selected || $clear_search_options || $sorting_selected || $delete_selected){
                        //prepare array containing response
                        array_push($response, $types_new_options, $type_tags, $new_locations_list, $sorting_tag);

                        // send the response
                        echo json_encode($response); 
                    }                    
                    else{
                        if(count($_SESSION['restaurant_types_selected']) != 0 )
                            $locations_list = $restaurant_only_of_types;
                    }
                }
            }
        }


        /*
         * Returns a $location list html item as 
         * a string.
         *
         * @author Patrice Boulet
         */
        function get_location_html_items($locations_list){
           $location_html_item = "";
            foreach($locations_list as $location){
                $location_html_item .= 
                '<div class="list-group-item" data-locationid="' . $location->location_id . '"><div class="row">
                    <div class="col-sm-5">
                        <a href="Location.php?locationid=' . $location->location_id . '">
                        <h4>' . $location->name . '</a></br>';
                
                $address_for_gmaps_splitted = explode( ",", $location->address );
                $address_for_gmaps = join('+', $address_for_gmaps_splitted);
                
                $location_html_item .= '<a href="https://www.google.com/maps/dir/Current+Location/' . $address_for_gmaps . '"><small class="list-group-item-text">' . $location->address .'</small></a>
                        </h4>
                    </div>
                    <div class="col-sm-3">
                    <div class="row"><span class="col-sm-12"><span style="font-size:16pt; font-weight:bold;">' . 
                            $location->avg_rating . '</span>
                            <span style="font-size:10pt">out of 5 </span><a style="font-size:8pt" href="Location.php?locationid=' . $location->location_id . '#ratings">(' . $location->total_num_ratings .' ratings)</a></span>
                    </div>';
                
                    // add black $ for actual price avg
                    for( $i = 0; $i < $location->avg_price; $i++){
                        $location_html_item .= '<h6 <span class="glyphicon glyphicon-usd" style="color:black"></span></h6>';
                    }

                    // add the subtraction of 5 by avg price of grey $
                    for( $i = 0; $i < 5-$location->avg_price; $i++){
                        $location_html_item .= '<h6 <span class="glyphicon glyphicon-usd" style="color:#DCDCDC"></span></h6>';
                    }
                   $location_html_item .= 
                        '</div>
                    <div class="col-sm-3">';
                    
                    foreach($_SESSION[$location->name . '-types'] as $cuisine_type){
                      $location_html_item .= '<span class="tagcloud tag label label-info">' . $cuisine_type . '</span>';
                    }
                
                $location_html_item .= '</div>';
                
                // if the admin is logged in, give the privileges to delete a location
                if( isset($_SESSION['username']) ){
                    if( $_SESSION['username'] == 'admin'){
                        $location_html_item .= '<div class="col-sm-1 adminControl"><button class="btn btn-danger" onclick="deleteLocation(parseInt(this.parentNode.parentNode.parentNode.dataset.locationid))">
                                                    <span class="glyphicon glyphicon-trash" style="color:black"> </span>
                                                </button></div>';
                    }
                }
                
                $location_html_item .= '</div></div>';
            }
            return $location_html_item;
        }

        /*
         * Returns a cloud $cuisine_type tag html item as 
         * a string.
         *
         * @author Patrice Boulet
         */
        function get_cloud_cuisine_tag_html_item($cuisine_type){
            return '<span class="tagcloud tag label label-info">' . $cuisine_type .
                                '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>';
        }

        /*
         * Returns a sorting tag html item as 
         * a string.
         *
         * @author Patrice Boulet
         */
        function get_selected_sorting_tag_html_item(){
            $tag_text = "";
            switch($_SESSION['locations_sorting_selected']){
                case 'avg_rating ASC':
                    $tag_text = 'Global Rating (ascending)';
                    break;
                case 'avg_rating DESC':
                    $tag_text = 'Global Rating (descending)';
                    break;
                case 'avg_price ASC':
                    $tag_text = 'Price (ascending)';
                    break;
                case 'avg_price DESC':
                    $tag_text = 'Price (descending)';
                    break;
                case 'avg_service ASC':
                    $tag_text = 'Service Rating (ascending)';
                    break;
                case 'avg_service DESC':
                    $tag_text = 'Service Rating (descending)';
                    break;
                case 'avg_food ASC':
                    $tag_text = 'Food Rating (ascending)';
                    break;
                case 'avg_food DESC':
                    $tag_text = 'Food Rating (descending)';
                    break;
                case 'avg_ambiance ASC':
                    $tag_text = 'Ambiance Rating (ascending)';
                    break;
                case 'avg_ambiance DESC':
                    $tag_text = 'Ambiance Rating (descending)';
                    break;
                case 'popularity ASC':
                    $tag_text = 'Popularity (ascending)';
                    break;
                case 'popularity DESC':
                    $tag_text = 'Popularity (descending)';
                    break;
            }
            
            return '<span class="tagcloud tag label label-info">' . $tag_text .
                            '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>';
        }

?>