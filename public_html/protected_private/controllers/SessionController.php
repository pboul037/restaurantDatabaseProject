<?php

// Session Controller.
// @author Patrice Boulet

session_start();
if(isset($_POST['logout'])){
    unset($_SESSION['username']);
    unset($_SESSION['rater_id']);
}
if(isset($_POST['check_logged_in'])){
    echo isset($_SESSION['username']);
}
?>