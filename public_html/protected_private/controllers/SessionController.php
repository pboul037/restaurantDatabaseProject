<?php
session_start();

if(isset($_POST['logout'])){
    unset($_SESSION['username']);
}

?>