<?php 

    // include configuration
    require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');

    $dal =  new DAL();

    /**
      * Gets the rater types to populate the select 
      * input of the signup form.
      *
      * @author Patrice Boulet
      */
    if( isset($_POST['rater_types'])){
        $rater_types = "<option value='' disabled selected>Choose Rater Type...</option>";
        foreach($dal->get_all_rater_types() as $rater_type){
            $rater_types .= "<option value='" . $rater_type->type . "'>" . $rater_type->type . "</option>";
        }
        echo $rater_types;
    }

    /**
      * Handles the submit request from the signup form.
      *
      * @author Patrice Boulet
      */
    if(isset($_POST['signup_form_data'])){
        $signup_form_data = json_decode($_POST['signup_form_data']);
        
    }
?>