<?php 
    //Add menu item controller.
    //@author Patrice Boulet

    session_start();
    // include configuration
    require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');

    $dal =  new DAL();

    if(isset($_POST['update_helpfulness'])){
        $helpful = $_POST['helpful'];
        $rater_id = $_POST['rater_id'];
        $rating_id = $_POST['rating_id'];
        
        $dal->update_helpfulness($helpful, $rating_id, $rater_id);
    }
?>