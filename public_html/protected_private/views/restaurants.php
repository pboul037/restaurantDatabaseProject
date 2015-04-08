<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Restaurant Ratings</title>

    <?php 
        // controller 
        require_once(dirname(dirname(__FILE__)) . '\controllers\RestaurantsController.php');
        
        // modal dialogs 
        include('/SignupModal.html'); 
        include('/LoginModal.html'); 
    ?>
    
    <!-- Bootstrap Core CSS -->
    <link href="../../../framwork_dir/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../../../framwork_dir/bootstrap/css/homepage.css" rel="stylesheet">
    <link href="../../css/default.css" rel="stylesheet">
    
    <!-- Bootrap JS, jQuery and toastr-->
    <script src="../../../framwork_dir/bootstrap/js/jquery.js"></script>
    <script src="../../../framwork_dir/bootstrap/js/bootstrap.js"></script>
    <script src="../../../framwork_dir/notify/notify.min.js"></script>
    
    <!-- Form validation & session control -->
    <script src="../../../public_html/protected_private/js/jQueryFormValidator.js"></script>
    <script src="../../../public_html/protected_private/js/sessionControl.js"></script>
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
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
                <ul id="sessionButtons" class="nav navbar-nav pull-right">
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

        <div class="row">
            <div class="col-md-3">
                <p class="lead">Find a Restaurant</p>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <select id="types_select" class="btn btn-default dropdown-toggle" onchange="updateSelectedTypes(this.value)">
                                <option value="" disabled selected>Show only type(s)...</option>
                                <?php foreach($restaurant_types as $type) { ?>
                                <option value='<?php echo $type->name; ?>'>
                                    <?php echo $type->name; echo ' (' . $type->count . ')'; ?></option><?php } ?>
                            </select>
                        </div>
                        <div class="row">
                            <div id="selected_types_tags">
                                <?php foreach($_SESSION['restaurant_types_selected'] as $already_selected){ ?>
                                    <span class="tagcloud tag label label-info"><?php echo $already_selected ?>
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></span>
                                <?php } ?>  
                            </div>
                        </div>
                        <div class="row">
                            <select id="types_select" class="btn btn-default dropdown-toggle" onchange="updateSorting(this.value)">
                                <option value="" disabled selected>Sort by...</option>
                                <option value='avg_rating ASC'>Global Rating (ascending)</option>
                                <option value='avg_rating DESC'>Global Rating (descending)</option>
                                <option value='avg_price ASC'>Price (ascending)</option>
                                <option value='avg_price DESC'>Price (descending)</option>
                                <option value='avg_food ASC'>Food Rating(ascending)</option>
                                <option value='avg_food DESC'>Food Rating(descending)</option>
                                <option value='avg_service ASC'>Service Rating (ascending)</option>
                                <option value='avg_service DESC'>Service Rating (descending)</option>
                                <option value='avg_ambiance ASC'>Ambiance Rating (ascending)</option>
                                <option value='avg_ambiance DESC'>Ambiance Rating (descending)</option>
                                <option value='popularity ASC'>Popularity (ascending)</option>
                                <option value='popularity DESC'>Popularity (descending)</option>
                            </select>
                        </div>
                        <div class="row">
                            <div id="selected_sorting_tag">
                                <?php if( isset($_SESSION['locations_sorting_selected'])){ 
                                    echo $sorting_tag;
                                } ?>
                            </div>
                        </div>
                        <div class="row">
                            <button id="clear_search_options_btn" type="button" class="btn btn-default" 
                                onclick="clearAllSearchOptions()" 
                                    <?php if( count($_SESSION['restaurant_types_selected']) == 0 && !isset($_SESSION['locations_sorting_selected']))                                            echo 'disabled'; ?>> Clear search options</button>
                        </div>
                    </div>
                    <script type="text/javascript">     
                        /*
                         * Updates the GUI when a type filter selection is
                         * made from the dropdown.
                         *
                         * @author Patrice Boulet
                         */
                        function updateSelectedTypes(selected) {
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/RestaurantsController.php',
                                data: 'type_selected=' + selected,
                                success: function (data) {
                                    updateLocationListHtmlElements(data, false, true, false);
                                }
                            });
                        }
                        
                        /*
                         * Updates GUI when a sorting options is selected from 
                         * the dropdown menu.
                         *
                         * @author Patrice Boulet
                         */
                        function updateSorting(selected){
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/RestaurantsController.php',
                                data: 'sorting_selected=' + selected,
                                success: function (data) {
                                    updateLocationListHtmlElements(data, false, false, true);
                                }
                            });
                        }
                        
                        /*
                         * Updates the GUI when clear all search options is selected. 
                         *
                         * @author Patrice Boulet
                         */
                        function clearAllSearchOptions(){
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/RestaurantsController.php',
                                data: 'clear_all_search_options=' + 'true',
                                success: function (data) {
                                    updateLocationListHtmlElements(data, true, true, true)
                                }
                            });
                        }
                        
                        /*
                         * Updates the locations list element in the GUI.
                         *
                         * @author Patrice Boulet
                         */
                        function updateLocationListHtmlElements(response, disableClearSearchOptions, updateTypesMenuAndTags, updateSortingTag){
                            var response = $.parseJSON(response);
                            //update restaurant list
                            $('#restaurant_list').html(response[2]);
                            // disable the clear search options button
                            $('#clear_search_options_btn').prop('disabled', disableClearSearchOptions);
                            if( updateTypesMenuAndTags )
                                updateMenuAndTagsHtmlElements(response);
                            if( updateSortingTag )
                                updateSelectedSortingTag(response);
                        }
                        
                        /*
                         * Updates the restaurant type select options and tags cloud in GUI.
                         *
                         * @author Patrice Boulet
                         */
                        function updateMenuAndTagsHtmlElements(response){
                            // update types select dropdown options
                            $('#types_select').html(response[0]);
                            //update type tags cloud
                            $('#selected_types_tags').html(response[1]);
                        }
                        
                        /*
                         * Updates the sorting tag for the sorting dropdown.
                         *
                         * @author Patrice Boulet
                         */
                        function updateSelectedSortingTag(response){
                            $('#selected_sorting_tag').html(response[3]);
                        }
                    </script>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row">
                <div class="row">
                    <div class="col-sm-12 col-lg-12 col-md-12">
                        <div id="restaurant_list" class="list-group">
                        <?php echo get_location_html_items($locations_list) ?>
                        </div>
                    </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
    <!-- /.container -->
    <div class="container">

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Patrice Boulet, Qasim Ahmed, & Junyi Dai 2015</p>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->
</body>

</html>