<?php
        //session_start();

        //Retrieve student number from session object
        //if(!isset($_SESSION['studentnum'])){
        //    echo "Please" . "<a href='Login.php'>Login</a>";
        //    exit;
        //}
        
        // include configuration
        require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');
        
        class Restaurants {
            private $dal = null;                    // data access layer
            
            public $restaurant_list_data = null;   // list of restaurant data
            
            public $restaurant_types = null;       // all restaurant types along with count of locations for them
            
            public function __construct() {
                $dal =  new DAL();
                $this->restaurant_list_data = $dal->get_all_restaurants();
                $this->restaurant_types = $dal->get_restaurant_types_with_count();
            }

        }
?>