<?php 

    session_start();
    // include configuration
    require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');

    $dal =  new DAL();

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
        
        $dal->add_new_location_rating($location_id, $rater_id, $price, $food, $ambiance, $service, $comments, $avg_rating);
    }
?>