<?php

// include data access layer
require_once(dirname(dirname(dirname(__FILE__))) . '\controllers\DAL.php');

// database config
define ( 'DB_HOST', 'localhost' );
define ( 'DB_PORT', 5432);
define ( 'DB_NAME', 'postgres' );
define ( 'DB_USER', 'postgres' );
define ( 'DB_PASSWORD', 'csi2132');

?>