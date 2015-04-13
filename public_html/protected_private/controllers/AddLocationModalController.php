<?php 
    //Add location controller.
    //@author Patrice Boulet

    session_start();
    // include configuration
    require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');

    $dal =  new DAL();
    
    /*
     * Gets the data to populate the add rating modal from db.
     */
    if( isset($_POST['get_all_restaurants'])){
        $all_restaurants = $dal->get_all_restaurants();
        $all_restaurant_types = $dal->get_restaurant_types();
        $response = array();
        
        $all_restaurants_options = "<option value='' selected>None selected</option>";
        $all_restaurant_types_options = "<option value='' disabled selected>None selected</option>";
        
        foreach($all_restaurants as $restaurant){
            $all_restaurants_options .= "<option value='" . $restaurant->restaurant_id . "'>" . $restaurant->name . "</option>";
        }
        
        $all_restaurant_types_options = "<option value='' disabled>None selected</option>";
        
        foreach($all_restaurant_types as $type){
            $all_restaurant_types_options .= "<option value='" . $type->type_id . "'>" . $type->_name . "</option>";
        }
        
        array_push($response, $all_restaurants_options,  $all_restaurant_types_options);

        echo json_encode($response);
    }


    /**
      * Handles the submit request from the add location form.
      *
      * @author Patrice Boulet
      */
    if(isset($_POST['add_location'])){
        $response = json_decode($_POST['add_location_data']);
        
        $restaurant_id = $response[0];
        $restaurantName = $response[1]; 
        $restaurantURL = $response[2];
        $typesArray = json_decode($_POST['add_location_types']);
                
        $openingDate = $response[3];
        $manager = $response[4];
        $phone = $response[5];
        $address = $response[6];
        $openingHoursMon = $response[7];
        $openingHoursTuesday = $response[8];
        $openingHoursWednesday = $response[9];
        $openingHoursThursday = $response[10];
        $openingHoursFriday = $response[11];
        $openingHoursSaturday = $response[12];
        $openingHoursSunday = $response[13];
        
        $openingHours = "MON: " . $openingHoursMon .
            ", TUE: " . $openingHoursTuesday .
            ", WED: " . $openingHoursWednesday .
            ", THU: " . $openingHoursThursday .
            ", FRI: " . $openingHoursFriday .
            ", SAT: " . $openingHoursSaturday .
            ", SUN: " . $openingHoursSunday;
        
        if( $restaurant_id === ""){
            $new_restaurant_result = $dal->add_restaurant($restaurantName, $restaurantURL, $typesArray);
            $restaurant_id = $new_restaurant_result[0]->restaurant_id;
        }
        
        $dal->add_location($openingDate, $manager, $phone, $address, $openingHours, $restaurant_id);
    }
?>