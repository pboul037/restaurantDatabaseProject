<?php 

    session_start();
    // include configuration
    require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');

    $dal =  new DAL();
    
    /*
     * Gets the data to populate the add rating modal from db.
     */
    if( isset($_POST['get_categories'])){
        $typeSel = $_POST['get_categories'];
        
        $categories_result = $dal->get_categories_by_type($typeSel);
        $categories_response = array();
        foreach($categories_result as $category){
            array_push($categories_response, $category->category);
        }

        echo json_encode($categories_response);
    }


    /**
      * Handles the submit request from the add location menu item form.
      *
      * @author Patrice Boulet
      */
    if(isset($_POST['add_menu_item'])){
        $response = json_decode($_POST['add_menu_item']);
        $name = $response[0];
        $description = $response[1];
        $price = $response[2];
        $type = $response[3];
        $category = $response[4];
        $location_id = $response[5];
        
        $dal->add_menu_item($location_id, $name, $type, $category, $description, $price);
    }
?>