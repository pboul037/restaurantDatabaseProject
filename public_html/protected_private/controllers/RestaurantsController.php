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
        $restaurant_list_data = $dal->get_all_restaurants();
        $restaurant_types = $dal->get_restaurant_types_with_count_as_RestaurantTypesWithCount();
        $selected_types = array();

        if (!isset($_SESSION['restaurant_types_selected']))
           $_SESSION['restaurant_types_selected'] =  array();

        // if data are received via POST, with index of 'types_selected'
        if (isset($_POST['types_selected'])) {
            $selectedvalue = $_POST['types_selected'];             // get data
            $type = explode('(', $selectedvalue)[0];
            
            for ($i = 0; $i < count($restaurant_types); $i++) {
                //echo $restaurant_types[$i]->name;  
              if( strcmp($type,$restaurant_types[$i]->name) == 0){
                array_push($_SESSION['restaurant_types_selected'], $restaurant_types[$i]->name); 
              }
            }
            
            $response = array();
            $new_options = ""; 
            $type_tags = "";
            
            // prepare the new options to send in response
            $new_options .= '<select id="types_select" class="btn btn-default dropdown-toggle" onchange="updateSelectedTypes(this.value)">      <option value="" disabled selected>Show only type(s)...</option>';  
            
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
                    $new_options .= "<option value='" . $restaurant_types[$i]->name . "'>" . $restaurant_types[$i]->name . ' (' . $restaurant_types[$i]->count . ')</option>';                                       }
            }
            $new_options .= '</select>'; 
            
            
            // prepare the new type tags
            foreach($_SESSION['restaurant_types_selected'] as $already_selected){
                //echo $already_selected;
                $type_tags .= '<span class="tagcloud tag label label-info">' . $already_selected .
                    '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>';
            }      

            array_push($response, $new_options, $type_tags);
            // send the response

            echo json_encode($response);            
        }
?>