<?php
        /*
         * Controller for the restaurants page.
         *
         * @author Qasim Ahmed
         */

        session_start();
        
        // include configuration
        require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');
        $dal =  new DAL();                          // data access layer
        
        $rater_id = $_POST['rater_id'];
        $dal->deleteRater($rater_id);

        echo 'Success for deletion of '. $rater_id;

?>