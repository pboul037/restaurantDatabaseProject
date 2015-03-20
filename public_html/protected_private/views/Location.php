<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Restaurant Ratings</title>
    
    <!-- controller -->
    <?php require_once(dirname(dirname(__FILE__)) . '\controllers\LocationController.php');?>
    
    <!-- Bootstrap Core CSS -->
    <link href="../../../framwork_dir/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../../../framwork_dir/bootstrap/css/homepage.css" rel="stylesheet">

    
    <!-- jQuery -->
    <script src="../../../framwork_dir/bootstrap/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../../../framwork_dir/bootstrap/js/bootstrap.min.js"></script>
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    
    <?php
        
        //session_start();

        //Retrieve student number from session object
        //if(!isset($_SESSION['studentnum'])){
        //    echo "Please" . "<a href='Login.php'>Login</a>";
        //    exit;
        //}
        
        // include configuration
        require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');
        
        // instantiate a data access layer
        $dal =  new DAL();
        
    ?>
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Restaurant Ratings</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
               <ul class="nav navbar-nav">
                    <li>
                        <a href="home.php">Home</a>
                    </li>
                    <li>
                        <a href="restaurants.php">Restaurants</a>
                    </li>
                    <li>
                        <a href="raters.php">Raters</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">
 
    <!-------->
    <div id="content">
        <h3><?php echo $details->_name; ?></h3>
        <h6><small style="font-size:10pt"><a href="https://www.google.com/maps/dir/Current+Location/<?php echo $address_for_gmaps; ?>"><?php echo $details->street_address; ?></small></h6></h6></a>                <div class="row">
        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
            <li class="active"><a href="#details" data-toggle="tab">Details</a></li>
            <li><a href="#ratings" data-toggle="tab">Ratings</a></li>
            <li><a href="#menu" data-toggle="tab">Menu</a></li>
        </ul>
        <div id="my-tab-content" class="tab-content">
            <div class="tab-pane active" id="details">
                <div class="row">
                <h5><?php echo $details->phone_number; ?><h5>
                </div>
                <div class="row">
                    <p>Opening date: <?php echo $details->first_open_date; ?></p>
                </div>
                <div class="row">
                    <p>Manager: <?php echo $details->manager_name; ?></p>
                </div>
                <div class="row">
                    <p>Opening Hours:</p>
                </div>
                <div><?php for($i = 0; $i < 7; $i++){ ?>
                    <span style="font-size:6pt"><?php echo $opening_hours[$i]; ?></span></br>
                <?php } ?></div>
            </div>
            <div class="tab-pane" id="ratings">
                <h1>Ratings</h1>
                <p>Location ratings</p>
            </div>
            <div class="tab-pane" id="menu">
                <div class="row">
                    </br>
                </div>
                <div class="row">
                    <div class="col-sm-8">
                        <h3>Eat</h3>
                          <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                              <div class="panel-heading">
                                <h4 class="panel-title">
                                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Appetizers</a>
                                </h4>
                              </div>
                              <div id="collapse1" class="panel-collapse collapse in">
                                <ul class="list-group">
                                    <?php echo $appetizers_html; ?>
                                </ul>
                              </div>
                            </div>
                            <div class="panel panel-default">
                              <div class="panel-heading">
                                <h4 class="panel-title">
                                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Main Dishes</a>
                                </h4>
                              </div>
                              <div id="collapse2" class="panel-collapse collapse">
                                <ul class="list-group">
                                    <?php echo $mains_html; ?>
                                </ul>
                              </div>
                            </div>
                            <div class="panel panel-default">
                              <div class="panel-heading">
                                <h4 class="panel-title">
                                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Desserts</a>
                                </h4>
                              </div>
                              <div id="collapse3" class="panel-collapse collapse">
                                <ul class="list-group">
                                   <?php echo $desserts_html; ?>
                                </ul>
                              </div>
                            </div>
                          </div> 
                    </div>
                    <div class="col-sm-4">
                        <h3>Drink</h3>
                          <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <?php foreach( $beverages_menu_items_by_category as $menu_items_html){ ?>
                                <div class="panel-heading">
                                <h4 class="panel-title">
                                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse4"></a>
                                </h4>
                              </div>
                              <div id="collapse4" class="panel-collapse collapse in">
                                <ul class="list-group">
                                    <?php echo $menu_items_html; ?>
                                </ul>
                              </div>
                                <?php } ?>
                            </div>
                            </div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(function () {
            $('#tabs').tab();
        });
    </script>    
    </div> <!-- container -->
</body>

</html>