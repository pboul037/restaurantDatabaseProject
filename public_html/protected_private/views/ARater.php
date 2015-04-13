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
        require_once(dirname(dirname(__FILE__)) . '\controllers\ARaterController.php');
        
        // modal dialogs 
        include('/SignupModal.html'); 
        include('/LoginModal.html');
        include('/AddRatingModal.html');
    ?>
    
    <!-- Form validation and session control -->
    <script src="../../../public_html/protected_private/js/jQueryFormValidator.js"></script>
    <script src="../../../public_html/protected_private/js/sessionControl.js"></script>
    <script src="../../../public_html/protected_private/js/AddRatings.js"></script>
    
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

     <script type="text/javascript"> 
        $(function () {
            $('#tabs').tab();
                var hash = window.location.hash; 
                // do some validation on the hash here
                hash && $('ul.nav a[href="' + hash + '"]').tab('show');
        });   

    </script>
</head>
<!-- 
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
                        <a href="raters.php">Raters</a>
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
                            <a id="usernameBtn" href="ARater.php?rater=' . $_SESSION['username'] . '" style="cursor: pointer">' . $_SESSION['username'] . '</a>
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
 <body>
<div class="container target">
    <div class="row">
        <div class="col-sm-12">
             <h1 class=""><?php 	echo $rater->_name ?></h1>

<div class="tab-pane" id="ratings">
                <div class="row">
                	<div class="col-sm-3"><img src="http://placehold.it/150x100"></div>
                    <div class="col-sm-8"><h1>Ratings</h1></div>
                    </div>
                <div class="row">
                    <div class="col-sm-12">
                    <div class="col-sm-3"></div>
                        <select id="types_select" class="btn btn-default dropdown-toggle" onchange="updateSorting(this.value)">    
                                <option value="" disabled selected>Sort by...</option>
                                <option value='_name'>Restaurant Name (ascending) </option>
                                <!-- <option value='rating_id ASC'>Rating ID (ascending) </option>
                                <option value='rating_id DESC'>Rating ID (descending) </option>
                                <option value='l.location_id ASC'>Location ID (ascending) </option>
                                <option value='l.location_id DESC'>Location ID (descending) </option> -->
                                <option value='date_written ASC'>Date Written (ascending) </option>
                                <option value='date_written DESC'>Date Written (descending) </option>
                                <option value='avg_rating ASC'>AVG Rating (ascending) </option>
                                <option value='avg_rating DESC'>AVG Rating (descending) </option>
                                <option value='food ASC'>Food Rating (ascending) </option>
                                <option value='food DESC'>Food Rating (descending) </option>
                                <option value='ambiance ASC'>Ambiance Rating(ascending) </option>
                                <option value='ambiance DESC'>Ambiance Rating(descending) </option>
                                <option value='service ASC'>Service Rating (ascending </option>
                                <option value='service DESC'>Service Rating (descending) </option>
                                <option value='price ASC'>Price (ascending) </option>
                                <option value='price DESC'>Price (descending) </option>
                        </select>
                        <button id="clear_search_options_btn" type="button" class="btn btn-default" 
                                onclick="clearAllSearchOptions()" 
                                    <?php if(!isset($_SESSION['raters_sorting']))        echo 'disabled'; ?>> Clear search options</button>
                        
                    </div>
        </div>
    </div>
  <br>
    <div class="row">
        <div class="col-sm-3">
            <!--left col-->
            <ul class="list-group">
                <li style="height:50px" class="list-group-item"> 
                    <span class="pull-left">
                        <span class="pull-left"></span><strong>Reputation:</strong></span>
                    </span>
                    <span class="pull-right">
                        <span class="glyphicon glyphicon-star" style="color:green"></span>
                        <span><?php echo $rater->rater_reputation; ?></span>
                    </span>
                </li>
                <li style="height:50px" class="list-group-item">
                    <span class="pull-left">
                        <span ></span><strong>Total ratings:</strong>
                    </span>
                    <span class="pull-right">
                        <span class="glyphicon glyphicon-stats" style="color:green"></span>
                        <span><?php echo $rater->total_num_ratings; ?></span>
                    </span>
                </li>
                <li class="list-group-item text-right"><span class="pull-left"><strong class="">Joined: </strong></span>  <?php 	echo $rater->join_date ?></li>
                <li class="list-group-item text-right"><span class="pull-left"><strong class="">Last Rating Date: </strong></span> <?php 	echo $last_rated ?></li>
                <li class="list-group-item text-right"><span class="pull-left"><strong class="">Type: </strong></span><?php 	echo $rater->_type ?></li>
                <li class="list-group-item text-right userControl adminControl"><span class="pull-left"></span> <?php 	echo deleteButton() ?></li>
            </ul>

        </div>
        
        <!--/col-3-->
        <div class="col-sm-9" style="" contenteditable="false">
              <div id="rater_ratings" class="list-group">
              <?php 	echo getAllRatingsForRaterHTML($rater_ratings) ?>
              </div>
            <div id="push"></div>
        </div>

        </footer>
        
        <script type="text/javascript">

        /*
        Function to update the sorting on the page
        @author Qasim Ahmed
        */
        function updateSorting(sorting_selected){
        	//Gets the rater name by manipulating the url
            var rater_name = ((window.location.search).split('='))[1];
            //alert("we made it");
				$.ajax({
    			type: "POST",
    			url: '../controllers/ARaterController.php',
    			data: {raters_sorting: sorting_selected, ratername: rater_name},
			
    			success: function (data) {
    					//console.log(data);
    					//console.log(JSON.parse(data));
    			        updateRatingsListHtmlElements(data, false);
    			        
    			        },
    			error: function(data){
    					alert("An error occured while applying the sorting");
    			}
				});
        }
/*
         * Updates the ratings list element in the GUI.
         *
         * @author Qasim Ahmed
         */
        function updateRatingsListHtmlElements(response, disableClearSearchOptions){
            var html_response = $.parseJSON(response);
            //var response = response;
            //update ratings lists
            console.log(html_response);
            $('#rater_ratings').html(html_response);
            // disable the clear search options button
            $('#clear_search_options_btn').prop('disabled', disableClearSearchOptions);
        }
        /*
        Function used to delete the account of a rater
        @author Qasim Ahmed
        */
        function deleteAccount(rater_id){
        	
        	$.ajax({
    			type: "POST",
    			url: '../controllers/DeleteRater.php',
    			data: {rater_id: rater_id},
			
    			success: function (data) {
    			     $.notify("Account was successfullly deleted.  You will now be redirected to the raters page.", "success");
    			     window.location.href = "raters.php";
                       setTimeout(function(){ 
                            location.reload();},3000
                        );
                },
    			error: function(data){
    					alert("An error occured while deleted the account");
    			}
				});
        	
        }
               /*
          * Updates the GUI when clear all search options is selected. 
          *
          * @author Qasim Ahmed
          */
         function clearAllSearchOptions(){
        	//Gets the rater name by manipulating the url
            var rater_name = ((window.location.search).split('='))[1];
            //alert("we made it");
				$.ajax({
    			type: "POST",
    			url: '../controllers/ARaterController.php',
    			data: {raters_sorting: true, clear_all_search_options: true, ratername: rater_name},
			
    			success: function (data) {
    					//console.log(data);
    					//console.log(JSON.parse(data));
    			        updateRatingsListHtmlElements(data, true);
    			        
    			        },
    			error: function(data){
    					alert("An error occured while applying the sorting");
    			}
				});
         }

        </script>     
        </div>
 </body>





</html>