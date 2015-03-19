<?php
        session_start();

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
            
            global $dal, $locations_list, $restaurant_types;
           
            $locations_list = $dal->get_all_restaurants();
            $restaurant_types = $dal->get_restaurant_types_with_count_as_RestaurantTypesWithCount();
            
            $type_selected = isset($_POST['type_selected']);
            $clear_search_options = isset($_POST['clear_all_search_options']);
            
            // initial load - no user specified search options
            if (!isset($_SESSION['restaurant_types_selected']))
                    $_SESSION['restaurant_types_selected'] =  array();
            
            if( !$type_selected && count($_SESSION['restaurant_types_selected']) == 0){
                return;
            }else{
                if($clear_search_options){
                    unset($_SESSION['restaurant_types_selected']);
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
                if($type_selected || $clear_search_options){
                    
                    $response = array();
                    $types_new_options = ""; 
                    $type_tags = "";
                    $new_locations_list = "";
                
                    // prepare the new options 
                    $types_new_options .= '<select id="types_select" class="btn btn-default dropdown-toggle"                                                                onchange="updateSelectedTypes(this.value)"><option value="" disabled selected>Show only type(s)...</option>'; 
                }
                
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
                
                if($type_selected || $clear_search_options)
                    $types_new_options .= '</select>';
                
                if( count($_SESSION['restaurant_types_selected']) !== 0 || $clear_search_options){
                    if(!$clear_search_options)
                        $restaurant_only_of_types = $dal->get_only_restaurants_of_types($_SESSION['restaurant_types_selected']);

                    if($type_selected){
                        foreach($_SESSION['restaurant_types_selected'] as $already_selected){
                            $type_tags .= get_cloud_cuisine_tag_html_item($already_selected);
                        }
                        $new_locations_list .= get_location_html_item($restaurant_only_of_types);
                    }
                    
                    if($clear_search_options){
                        $new_locations_list .= get_location_html_item($locations_list);
                    }
                    
                    if($type_selected || $clear_search_options){
                        //prepare array containing response
                        array_push($response, $types_new_options, $type_tags, $new_locations_list);

                        // send the response
                        echo json_encode($response); 
                    }
                    
                    else{
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
        function get_location_html_item($locations_list){
           $location_html_item = "";
            foreach($locations_list as $location){
                $location_html_item .= '<a href="#" class="list-group-item">
                            <div class="pull-right">';
                foreach($_SESSION[$location->name . '-types'] as $cuisine_type){
                      $location_html_item .= '<span class="tagcloud tag label label-info">' . $cuisine_type . '</span>';
                }
                $location_html_item .= '</div>
                        <h4>' . $location->name . '</h4>
                        <!-- <img src="http://placehold.it/320x150" alt=""> -->
                        <p class="list-group-item-text">' . $location->address . 
                        '<!-- <a target="_blank" href="http://www.bootsnipp.com">Bootsnipp - http://bootsnipp.com</a>.--></p>
                        <div class="ratings">
                            <p class="pull-right">15 reviews</p>
                            <p>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                            </p>
                        </div>
                      </a>
                      ';
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
?>