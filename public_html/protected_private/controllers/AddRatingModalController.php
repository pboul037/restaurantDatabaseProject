/*
  * Add Rating Modal component
  *
  * @author Patrice Boulet
  */

<?php 

    session_start();
    // include configuration
    require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');

    $dal =  new DAL();
    
    /*
     * Gets the data to populate the add rating modal from db.
     */
    if( isset($_POST['add_rating'])){
        $location_id = $_POST['add_rating'];
            
        $response = array();
        
        $drinks_available_html = "";
        $food_available_html = "";
        
        $drinks_available = $dal->get_menu_items($location_id, 'drink', null);
        $food_available = $dal->get_menu_items($location_id, 'food', null);
         
        if( count($drinks_available) === 0){
            $drinks_available_html .= "<option value='noDrinksAvail' disabled>No drinks available</option>";
        }else{
            foreach($drinks_available as $drink){
                $drinks_available_html .= "<option value='" . $drink->item_id . "'>" . $drink->_name . "</option>";
            }
        }
        
        if( count($food_available) === 0){
            $food_available_html .= "<option value='noFoodAvail' disabled>No meals available</option>";
        }else{
            foreach($food_available as $food){
                $food_available_html .= "<option value='" . $food->item_id . "'>" . $food->_name . "</option>";
            }
        }
        array_push($response, $drinks_available_html, $food_available_html);
        echo json_encode($response);
    }


    /**
      * Handles the submit request from the add location rating form.
      *
      * @author Patrice Boulet
      */
    if(isset($_POST['add_location_rating_form_data'])){
        $add_location_rating_form_data = json_decode($_POST['add_location_rating_form_data']);
        
        $food = $add_location_rating_form_data[0];
        $service = $add_location_rating_form_data[1];
        $ambiance = $add_location_rating_form_data[2];
        $price = $add_location_rating_form_data[3];
        $comments = $add_location_rating_form_data[4];
        $location_id = $add_location_rating_form_data[5];
        $avg_rating = round(($food + $service + $ambiance)/3, 1);
        $rater_id = $_SESSION['rater_id'];
        
        $rating_id = $dal->add_new_location_rating($location_id, $rater_id, $price, $food, $ambiance, $service, $comments, $avg_rating);
        
        echo $rating_id[0]->rating_id;
    }

    /**
      * Handles the submit request from the add location rating form for the menu items.
      *
      * @author Patrice Boulet
      */
    if(isset($_POST['add_rating_menu_items'])){
        
        $drinks = json_decode($_POST['add_rating_menu_drinks']);
        $food = json_decode($_POST['add_rating_menu_food']);
        $rating_id = json_decode($_POST['add_rating_menu_rating_id']);
        
        //echo count($food) . "rating_id : " . $rating_id;
        foreach($drinks as $item_id){
            $dal->add_rating_item_no_rating($item_id, $rating_id);
            usleep(100000);
        }
        
        foreach($food as $item_id){
            $dal->add_rating_item_no_rating($item_id, $rating_id);
            usleep(100000);
        }
    }




?>