<?php 
    session_start();
    // include configuration
    require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');

    $dal =  new DAL();

    /**
      * Handles the submit request from the login form.
      *
      * @author Patrice Boulet
      */
    if(isset($_POST['login_form_data'])){
        
        $user_is_admin = false;
        $login_form_data = json_decode($_POST['login_form_data']);
        $matching_credentials = $dal->check_credentials($login_form_data[0], $login_form_data[1]);
        
        if(count($matching_credentials) > 0){ 
            $_SESSION['username'] = $matching_credentials[0]->name;
            $_SESSION['rater_id'] = $matching_credentials[0]->rater_id;
            $user_is_admin = $matching_credentials[0]->name === 'admin';
        }
        $response = array();
        array_push($response, count($matching_credentials));
        array_push($response, $user_is_admin);
        
        echo json_encode($response);
    }
?>