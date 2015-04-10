/*
  * Restaurants page controller.
  *
  * @author Patrice Boulet
  */


<?php
        /*
         * Controller for the restaurants page.
         *
         * @author Patrice Boulet
         */

        session_start();
        
        // include configuration
        require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');
        $dal =  new DAL();                          // data access layer
        $locations_list;                            // an array of all the locations 
        $restaurant_types;                          // an array of all the restaurant types with the # of locations in the db of this type
        $each_location_types = array();                                 
        $selected_cuisine_types = array();  
        $rating_filters = array();                  // wrapper array that contains all the rating filters
        
        /*
         * Ajax response array.  Content:
         * response[0] = types dropdown options (html)
         * response[1] = types tag cloud (html)
         * response[2] = locations list (html)
         * response[3] = sorting tag (html)
         * response[4] = rating filters checkbox inputs (html)
         */
        $response = array();   
        
        /*
         * HTML elements to inject in the DOM upon GUI update
         */
        $types_new_options = "";                    // restaurant types dropdown options        
        $type_tags = "";                            // types tag cloud 
        $sorting_tag = "";                          // sorting tag 
        $new_locations_list = "";                   // list of locations
        $rating_filters_inputs = "";                // rating filters inputs 
        
        /*******STEP 1 :    Setup conditions to control the fetching of the data model and html elements to inject into the DOM
         *                  based on the status of the SESSION and POST arrays state. *******************************************/
        // control condictions variables
        $types_filter_sel = false;                  // true if one or more restaurant type filter is applied
        $sorting_sel = false;                       // true if a sorting is applied
        $clear_sel= false;                          // true if clear search options is selected
        $location_delete_sel = false;               // true if a location's deletion has been requested by admin    
        $rating_filter_sel = false;                 // true if a rating filter is selected
            
        // check the POST array for selections and update control conditions variables accordingly
        $clear_sel = isset($_POST['clear_all_search_options']);             // clear all search options has been selected
        $location_delete_sel = isset($_POST['delete_location']);            // a location's deletion has been requested by admin
        
                
        // no user specified filters selected so initialize the arrays
        if( !isset( $_SESSION['restaurant_types_selected'])){
            $_SESSION['restaurant_types_selected'] =  array(); 
        }
        if( !isset( $_SESSION['avg_global_r_filter'])){
            $_SESSION['avg_global_r_filter'] =  array(); 
        }
        if( !isset( $_SESSION['avg_food_r_filter'])){
            $_SESSION['avg_food_r_filter'] =  array(); 
        } 
        if( !isset( $_SESSION['avg_service_r_filter'])){
            $_SESSION['avg_service_r_filter'] =  array(); 
        }        
        if( !isset( $_SESSION['avg_ambiance_r_filter'])){
            $_SESSION['avg_ambiance_r_filter'] =  array(); 
        }


        // get all restaurant types with # of locations in the db of this type
        $restaurant_types = $dal->get_restaurant_types_with_count_as_RestaurantTypesWithCount(); 
        // a restaurant type filter has been selected, add it to the SESSION array of types selected
        if(isset($_POST['type_selected'])){                                   
            $selectedvalue = $_POST['type_selected'];            
            $type = explode('(', $selectedvalue)[0];
            for ($i = 0; $i < count($restaurant_types); $i++) { 
              if( strcmp($type,$restaurant_types[$i]->name) == 0){
                array_push($_SESSION['restaurant_types_selected'], $restaurant_types[$i]->name); 
              }
            }
        }

        // a rating filter has been selected
        if( isset($_POST['rating_filter']) && isset($_POST['rating_filter_value']) && isset($_POST['rating_filter_checked']) ){
            $r_filter_type = $_POST['rating_filter'];
            $r_filter_value = $_POST['rating_filter_value'];
            $r_filter_checked = $_POST['rating_filter_checked'] === 'true';
            
            if( $r_filter_checked )
                array_push($_SESSION[$r_filter_type . '_r_filter'], $r_filter_value);
            else{
                if(($key = array_search($r_filter_value, $_SESSION[$r_filter_type . '_r_filter'])) !== false) {
                    unset($_SESSION[$r_filter_type . '_r_filter'][$key]);
                }
            }   
        }
        
        // build a wrapper array to contain all the rating filters arrays
        array_push($rating_filters, $_SESSION['avg_global_r_filter'],
                                       $_SESSION['avg_food_r_filter'],
                                       $_SESSION['avg_service_r_filter'],
                                       $_SESSION['avg_ambiance_r_filter']);
        
        // a sorting has been selected
        if(isset($_POST['sorting_selected']))                                
            $_SESSION['locations_sorting_selected'] = $_POST['sorting_selected'];

        // update remaining control conditions variables
        $types_filter_sel = count($_SESSION['restaurant_types_selected']) !== 0;
        $sorting_sel = isset($_SESSION['locations_sorting_selected']);
        $rating_filter_sel = count($_SESSION['avg_global_r_filter']) > 0 ||
                               count($_SESSION['avg_food_r_filter']) > 0 ||
                               count($_SESSION['avg_service_r_filter']) > 0 ||
                               count($_SESSION['avg_ambiance_r_filter']) > 0;
        
        /********************STEP 2 : Data model fetching and html elements fetching*******************************/
         
        
        // initilize restaurant types dropdown options html
        $types_new_options .=  get_restaurant_types_dropdown_options_html($restaurant_types, $types_filter_sel, $clear_sel);
            
        // delete a location
        if($location_delete_sel){      
            $dal->delete_location($_POST['delete_location']);
        }


        // clear all search options
        if( $clear_sel ){ 
            unset($_SESSION['restaurant_types_selected']);
            unset($_SESSION['locations_sorting_selected']);
            unset($_SESSION['avg_global_r_filter']);
            unset($_SESSION['avg_food_r_filter']);
            unset($_SESSION['avg_service_r_filter']);
            unset($_SESSION['avg_ambiance_r_filter']);
            $_SESSION['restaurant_types_selected'] = array();
            $locations_list = $dal->get_locations_list(null, null, null);
            $type_new_options = get_restaurant_types_dropdown_options_html($restaurant_types, $types_filter_sel, $clear_sel);
            
        // sort locations list
        }else if($sorting_sel && !$types_filter_sel && !$rating_filter_sel){ 
            $locations_list = $dal->get_locations_list(null, $_SESSION['locations_sorting_selected'], null);
            $sorting_tag .= get_selected_sorting_tag_html_item();
            
         // filter locations list by type
        }else if(!$sorting_sel && $types_filter_sel && !$rating_filter_sel){
            $locations_list = $dal->get_locations_list($_SESSION['restaurant_types_selected'], null, null);
            $type_new_options = get_restaurant_types_dropdown_options_html($restaurant_types, $types_filter_sel, $clear_sel);
            $type_tags .= get_cloud_cuisine_tag_html_item($_SESSION['restaurant_types_selected']);
            
        // rating filter(s) only are applied to locations list
        }else if(!$sorting_sel && !$types_filter_sel && $rating_filter_sel){
            $locations_list = $dal->get_locations_list(null, null, $rating_filters);

        // sort & filter by type locations list
        }else if($sorting_sel && $types_filter_sel && !$rating_filter_sel){ 
            $locations_list = $dal->get_locations_list($_SESSION['restaurant_types_selected'], $_SESSION['locations_sorting_selected'],                                                                 null);
            $type_new_options = get_restaurant_types_dropdown_options_html($restaurant_types, $types_filter_sel, $clear_sel);
            $type_tags .= get_cloud_cuisine_tag_html_item($_SESSION['restaurant_types_selected']);
            $sorting_tag .= get_selected_sorting_tag_html_item();
            
        // sort & rating filter(s) applied
        }else if($sorting_sel && !$types_filter_sel && $rating_filter_sel){ 
            $locations_list = $dal->get_locations_list(null, $_SESSION['locations_sorting_selected'], $rating_filters);
            $sorting_tag .= get_selected_sorting_tag_html_item();
            
        // filter by type and rating filter(s) applied
        }else if(!$sorting_sel && $types_filter_sel && $rating_filter_sel){ 
            $locations_list = $dal->get_locations_list($_SESSION['restaurant_types_selected'], null, $rating_filters);
            $type_new_options = get_restaurant_types_dropdown_options_html($restaurant_types, $types_filter_sel, $clear_sel);
            $type_tags .= get_cloud_cuisine_tag_html_item($_SESSION['restaurant_types_selected']);
            
        // sort, filter by type and rating filter(s) applied
        }else if($sorting_sel && $types_filter_sel && $rating_filter_sel){ 
            $locations_list = $dal->get_locations_list($_SESSION['restaurant_types_selected'], $_SESSION['locations_sorting_selected'],                                                                 $rating_filters);
            $type_new_options = get_restaurant_types_dropdown_options_html($restaurant_types, $types_filter_sel, $clear_sel);
            $sorting_tag .= get_selected_sorting_tag_html_item();
            $type_tags .= get_cloud_cuisine_tag_html_item($_SESSION['restaurant_types_selected']);
            
        // basic locations list
        }else{
            $locations_list = $dal->get_locations_list(null, null, null);
        }

        // get the html elements for the locations list
        get_locations_types($locations_list);
        $new_locations_list .= get_location_html_items($locations_list);
        // send the ajax response as a JSON string if its a GUI dynamic request
        if( isset($_POST['type_selected']) || 
            isset($_POST['sorting_selected']) ||
            isset($_POST['clear_all_search_options']) ||          
            isset($_POST['delete_location']) ||
            (isset($_POST['rating_filter']) && isset($_POST['rating_filter']) && isset($_POST['rating_filter'])))
        {
            //prepare array containing response
            array_push($response, $types_new_options, $type_tags, $new_locations_list, $sorting_tag);
            echo json_encode($response); 
        }
        
        /******************************************END OF STEP 2************************************************/
            
            
        /*
         * Get cuisine types for each location in the list and put them in the in SESSION.
         *
         * @author Patrice Boulet
         */
        function get_locations_types ($locations_list) {
            global $dal;
            
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
         * Returns a $location list html item as 
         * a string.
         *
         * @author Patrice Boulet
         */
        function get_location_html_items($locations_list){
           $location_html_item = "";
            if( count($locations_list) < 1 ){
                $location_html_item .= 
                    '<div style="text-align:center" class ="col-sm-12">
                        <p>Sorry, no restaurant was found matching your search option(s).</p>
                    </div>';
            }
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
                      $location_html_item .= '<span class="tagcloud tag label label-warning">' . $cuisine_type . '</span>';
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
         * Returns the cloud of html cuisine type tags for
         * a location.
         *
         * @author Patrice Boulet
         */
        function get_cloud_cuisine_tag_html_item($restaurant_types_selected){
            $returned_type_tags = "";
            foreach($restaurant_types_selected as $cuisine_type){
                $returned_type_tags .= '<span class="tagcloud tag label label-warning">' . $cuisine_type .
                                '<!-- <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> --></span>';
            }
            return $returned_type_tags;
        }
            
        /*
         * Initialize the restaurants types filter dropdown options html.
         *
         * @author Patrice Boulet
         */
        function get_restaurant_types_dropdown_options_html($restaurant_types, $types_filter_sel, $clear_sel){
            $returned_types_new_options = '<select id="types_select" class="btn btn-default dropdown-toggle"                                                                onchange="updateSelectedTypes(this.value)"><option value="" disabled selected>Show only cuisine(s)...</option>';
            
            for ($i = 0; $i < count($restaurant_types); $i++) {
                        $found = false;
                        foreach($_SESSION['restaurant_types_selected'] as $already_selected){ 
                            if ( $restaurant_types[$i]->name == $already_selected ){
                                unset($restaurant_types[$i]);
                                $restaurant_types = array_values($restaurant_types);
                                $found = true;
                            } 
                        } 
                        if($found == false){
                            $returned_types_new_options .= "<option value='" . $restaurant_types[$i]->name . "'>" . 
                                $restaurant_types[$i]->name . ' (' . $restaurant_types[$i]->count . ')</option>';                            
                        }
            }
            
            $returned_types_new_options .= '</select>';
            return $returned_types_new_options;
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
            
            return '<span class="tagcloud tag label label-warning">' . $tag_text .
                            '<!-- <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> --></span>';
        }
?>