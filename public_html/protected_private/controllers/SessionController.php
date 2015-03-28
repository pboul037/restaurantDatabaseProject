<?php
session_start();

if(isset($_POST['logout'])){
    unset($_SESSION['username']);
}

if(isset($_POST['check_logged_in'])){
    echo isset($_SESSION['username']);
}

?>