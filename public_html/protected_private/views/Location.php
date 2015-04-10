<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Restaurant Ratings</title>
    
    <!-- Bootstrap Core CSS -->
    <link href="../../../framwork_dir/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../../css/default.css" rel="stylesheet">
    <link href="../../../framwork_dir/bootstrap/css/homepage.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="../../../framwork_dir/bootstrap-spinner/bootstrap-spinner.css" rel="stylesheet">

    
    <!-- jQuery -->
    <script src="../../../framwork_dir/bootstrap/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../../../framwork_dir/bootstrap/js/bootstrap.min.js"></script>
    
    <!-- Notify -->
    <script src="../../../framwork_dir/notify/notify.min.js"></script>
    
    <!-- Bootstrap rating input (stars) -->
    <script src="../../../framwork_dir/bootstrap-rating-input/bootstrap-rating-input.min.js"></script>
    
        <!-- controller -->
    <?php 
        // controller
        require_once(dirname(dirname(__FILE__)) . '\controllers\LocationController.php');
        
        // modal dialogs 
        include('/SignupModal.html'); 
        include('/LoginModal.html');
        include('/AddRatingModal.html');
        include('/AddMenuItemModal.html');
    ?>
    
    <!-- Form validation and session control -->
    <script src="../../../public_html/protected_private/js/jQueryFormValidator.js"></script>
    <script src="../../../public_html/protected_private/js/sessionControl.js"></script>
    <script src="../../../public_html/protected_private/js/AddRatings.js"></script>
    <script src="../../../public_html/protected_private/js/AddMenuItems.js"></script>
    <script src="../../../framwork_dir/bootstrap-spinner/jquery.spinner.min.js"></script>
    
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
    <script type="text/javascript"> 
        $(function () {
            $('#tabs').tab();
                var hash = window.location.hash; 
                // do some validation on the hash here
                hash && $('ul.nav a[href="' + hash + '"]').tab('show');
        });   
    </script>
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
                        <a href="restaurants.php">Restaurants</a>
                    </li>
                    <li>
                        <a href="raters.php">Ratings</a>
                    </li>
                </ul>
                <ul id ="sessionButtons" class="nav navbar-nav pull-right">
                    <?php 
                    if (!isset($_SESSION['username'])) { 
                        echo '<li>
                            <a id="loginBtn" style="cursor: pointer">Login</a>
                        </li>
                        <li>
                            <!-- Button trigger modal -->
                            <a id="signUpBtn" style="cursor: pointer">Sign up</a>
                        </li>';
                    }else{
                        echo '<li>
                            <a id="usernameBtn" style="cursor: pointer">' . $_SESSION['username'] . '</a>
                        </li>
                        <li>
                            <a id="logoutBtn" style="cursor: pointer">Log out</a>
                        </li>';
                    } ?>
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
        <h6><small style="font-size:10pt"><a href="https://www.google.com/maps/dir/Current+Location/<?php echo $address_for_gmaps; ?>"><?php echo $details->street_address; ?></small></h6></h6></a><div class="row">
        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
            <li class="active"><a href="#details" data-toggle="tab">Details</a></li>
            <li><a href="#ratings" data-toggle="tab">Ratings</a></li>
            <li><a href="#menu" data-toggle="tab">Menu</a></li>
            <div class="pull-right" style="padding-left:5px">
                <button class="btn btn-primary pull-right" onclick="addMenuItem()">Add a menu item</button>
            </div>
            <div class="pull-right">
                <button class="btn btn-primary pull-right" onclick="addRating()">Add a rating</button>
            </div>
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
                    <span style="font-size:10pt"><?php echo $opening_hours[$i]; ?></span></br>
                <?php } ?></div>
            </div>
            <div class="tab-pane" id="ratings">
                <div class="row">
                    <div class="col-sm-4"><h1>Ratings</h1></div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <select id="types_select" class="btn btn-default dropdown-toggle" onchange="updateSorting(this.value)">    
                                <option value="" disabled selected>Sort by...</option>
                                <option value='rater_ratings_for_this_loc DESC'>Most frequent raters first</option>
                                <option value='date_written ASC'>Date Written (ascending)</option>
                                <option value='date_written DESC'>Date Written (descending)</option>
                                <option value='food ASC'>Food Rating (ascending)</option>
                                <option value='food DESC'>Food Rating (descending)</option>
                                <option value='ambiance ASC'>Ambiance Rating(ascending)</option>
                                <option value='ambiance DESC'>Ambiance Rating(descending)</option>
                                <option value='rater_reputation ASC'>Rater Reputation (ascending)</option>
                                <option value='rater_reputation DESC'>Rater Reputation (descending)</option>
                                <option value='service ASC'>Service Rating (ascending)</option>
                                <option value='service DESC'>Service Rating (descending)</option>
                                <option value='price ASC'>Price (ascending)</option>
                                <option value='price DESC'>Price (descending)</option>
                        </select>
                        
                        <button id="clear_search_options_btn" type="button" class="btn btn-default" 
                                onclick="clearAllSearchOptions()" 
                                    <?php if(!isset($_SESSION['locations_ratings_sorting_selected']))        echo 'disabled'; ?>> Clear search options</button>
                    </div>
                    <script type="text/javascript">
                        /*
                         * Updates GUI when a sorting option is selected from 
                         * the dropdown menu.
                         *
                         * @author Junyi Dai
                         */
                        function updateSorting(selected){
                            var location_id = ((window.location.search).split('='))[1];
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/LocationController.php',
                                data: {locations_ratings_sorting_selected: selected, location_id: location_id},
                                success: function (data) {
                                    console.log(data);
                                    console.log(JSON.parse(data));
                                    updateRatingsListHtmlElements(data, false);
                                },
                                error: function(data){
                                	alert("Error ");
                                }
                            });
                        }
                        
                        /*
                         * Updates the GUI when clear all search options is selected. 
                         *
                         * @author Junyi Dai
                         */
                        function clearAllSearchOptions(){
                        	var location_id = ((window.location.search).split('='))[1];
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/LocationController.php',
                               // data: 'clear_all_search_options=' + 'true',
                                data: {locations_ratings_sorting_selected: true, clear_all_search_options : true, location_id: location_id},
                                success: function (data) {
                                	console.log(data);
                                    console.log(JSON.parse(data));
                                    updateRatingsListHtmlElements(data, true)
                                },
                                error: function(data){
                                	alert("error");
                                }
                            });
                        }
                        
                        /*
                         * Updates the ratings list element in the GUI.
                         *
                         * @author Junyi Dai
                         */
                        function updateRatingsListHtmlElements(response, disableClearSearchOptions){
                            var html_response = $.parseJSON(response);
                            //var response = response;
                            //update ratings lists
                            console.log(html_response[0]);
                            $('#location_ratings_list').html(html_response[0]);
                            // disable the clear search options button
                            $('#clear_search_options_btn').prop('disabled', disableClearSearchOptions);
                        }
                        
                        /*
                         * Sends add location rating form for validation. 
                         *
                         * @author Patrice Boulet
                         */
                        function submitAddLocationRatingForm(){
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/LocationController.php',
                                data: 'clear_all_search_options=' + 'true',
                                success: function (data) {
                                    updateRatingsListHtmlElements(data, true)
                                }
                            });
                        }
                        
                        /*
                         * Deletes a menu item.
                         *
                         * @author Patrice Boulet
                         */
                        function deleteMenuItem(item_id){
                            var item_id = item_id;
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/LocationController.php',
                                data: 'delete_menu_item=' + item_id,
                                success: function (response) {
                                    $.notify("The page will now reload in 3 seconds to update your changes...", "success");
                                    setTimeout(function(){ 
                                        window.location.hash = "#menu";
                                        location.reload();},3000
                                    );
                                }
                            });
                        }
                    </script>
                </div>
                
                 <div id="location_ratings_list" class="list-group">
                        <?php echo get_location_rating_html_items($location_ratings_list) ?>
                        </div>

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
                                <?php echo $appetizers_html; ?>
                            </div>
                            <div class="panel panel-default">
                                <?php echo $mains_html; ?>
                            </div>
                            <div class="panel panel-default">
                                <?php echo $desserts_html; ?>
                            </div>
                          </div> 
                    </div>
                    <div class="col-sm-4">
                        <h3>Drink</h3>
                          <div class="panel-group" id="accordion">
                            <?php foreach( $beverages_menu_items_by_category as $category=>$menu_items_html){ ?>
                            <div class="panel panel-default">
                                <?php echo $menu_items_html; ?>
                            </div>
                            <?php } ?>
                            </div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
    </div> <!-- container -->
</body>

</html>