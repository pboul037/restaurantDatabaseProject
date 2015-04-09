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
            <div class="row">
                <div style="text-align:center" class="page-header">
                    <h2>Find a Restaurant</h1>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h3 class="panel-title">Search options</h3>
                      </div>
                      <div class="panel-body">
                    <div class="col-sm-12">
                        <ul class="list-group">
                            <li style="border-style:none" class="list-group-item">
                        <div class="row">
                            <select id="sorting_select" class="btn btn-default dropdown-toggle" onchange="updateSorting(this.value)">
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
                        </li>
                        <li style="border-style:none" class="list-group-item">
                        <div class="row">
                            <select id="types_select" class="btn btn-default dropdown-toggle" onchange="updateSelectedTypes(this.value)">
                                <option value="" disabled selected>Show only cuisine(s)...</option>
                                <?php foreach($restaurant_types as $type) { ?>
                                <option value='<?php echo $type->name; ?>'>
                                    <?php echo $type->name; echo ' (' . $type->count . ')'; ?></option><?php } ?>
                            </select>
                        </div>
                        <div class="row">
                            <div id="selected_types_tags">
                                <?php if( isset($_SESSION['restaurant_types_selected'])){
                                    if(count($_SESSION['restaurant_types_selected']) > 0)
                                        echo $type_tags;
                                } ?> 
                            </div>
                        </div>
                        </li>
                        <li style="border-style:none" class="list-group-item">
                            <div class="row">
                                <h4>Show only:</h4>
                            </div>
                            <div class="row">
                            <div class="input-group">
                                <div style="text-align:center"><span class="lbl">Global rating of:</span></br>
                                    <input type="checkbox" id="filterGlobal1" name="filterGlobal1" value="avg_global=1">
                                        <label for="filterGlobal1">1</label>
                                    <input type="checkbox" id="filterGlobal2" name="filterGlobal2" value="avg_global=2">
                                        <label for="filterGlobal2">2</label>
                                    <input type="checkbox" id="filterGlobal3" name="filterGlobal3" value="avg_global=3">
                                        <label for="filterGlobal3">3</label>
                                    <input type="checkbox" id="filterGlobal4" name="filterGlobal4" value="avg_global=4">
                                        <label for="filterGlobal4">4</label>
                                    <input type="checkbox" id="filterGlobal5" name="filterGlobal5" value="avg_global=5">
                                        <label for="filterGlobal5">5</label>
                                </div>
                                <div style="text-align:center"><span class="lbl">Food rating of:</span></br>
                                    <input type="checkbox" id="filterFood1" name="filterFood1" value="avg_food=1">
                                        <label for="filterFood1">1</label>
                                    <input type="checkbox" id="filterFood2" name="filterFood2" value="avg_food=2">
                                        <label for="filterFood2">2</label>
                                    <input type="checkbox" id="filterFood3" name="filterFood3" value="avg_food=3">
                                        <label for="filterFood3">3</label>
                                    <input type="checkbox" id="filterFood4" name="filterFood4" value="avg_food=4">
                                        <label for="filterFood4">4</label>
                                    <input type="checkbox" id="filterFood5" name="filterFood5" value="avg_food=5">
                                        <label for="filterFood5">5</label>
                                </div>
                                <div style="text-align:center"><span class="lbl">Service rating of:</span></br>
                                    <input type="checkbox" id="filterService1" name="filterService1" value="avg_service=1">
                                        <label for="filterService1">1</label>
                                    <input type="checkbox" id="filterService2" name="filterService2" value="avg_service=2">
                                        <label for="filterService2">2</label>
                                    <input type="checkbox" id="filterService3" name="filterService3" value="avg_service=3">
                                        <label for="filterService3">3</label>
                                    <input type="checkbox" id="filterService4" name="filterService4" value="avg_service=4">
                                        <label for="filterService4">4</label>
                                    <input type="checkbox" id="filterService5" name="filterService5" value="avg_service=5">
                                        <label for="filterService5">5</label>
                                </div>
                                <div style="text-align:center"><span class="lbl">Ambiance rating of:</span></br>
                                    <input type="checkbox" id="filterAmbiance1" name="filterAmbiance1" value="avg_ambiance=1">
                                        <label for="filterAmbiance1">1</label>
                                    <input type="checkbox" id="filterAmbiance2" name="filterAmbiance2" value="avg_ambiance=2">
                                        <label for="filterAmbiance2">2</label>
                                    <input type="checkbox" id="filterAmbiance3" name="filterAmbiance3" value="avg_ambiance=3">
                                        <label for="filterAmbiance3">3</label>
                                    <input type="checkbox" id="filterAmbiance4" name="filterAmbiance4" value="avg_ambiance=4">
                                        <label for="filterAmbiance4">4</label>
                                    <input type="checkbox" id="filterAmbiance5" name="filterAmbiance5" value="avg_ambiance=5">
                                        <label for="filterAmbiance5">5</label>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li style="border-style:none"  class="list-group-item">
                        <div class="row">
                            <button id="clear_search_options_btn" type="button" class="btn btn-danger" 
                                onclick="clearAllSearchOptions()" 
                                    <?php if( count($_SESSION['restaurant_types_selected']) == 0 && !isset($_SESSION['locations_sorting_selected']))                                            echo 'disabled'; ?>> Clear search options</button>
                        </div>
                        </li>
                </ul>
                      </div>
                    </div>
                    </div>
                    <script type="text/javascript">     
                        // executed on page load
                        $(function(){
                            
                            // update the rating filter checkbox input state && clear search options btn state
                            $( "input[id*='filter']").on('click', updateRatingFilters);
                            updateRatingFiltersInputState();   
                            $.each($("input[id*='filter']"), function(key, filterInput) {
                                if( filterInput.checked == true){
                                    $('#clear_search_options_btn').prop('disabled', false);
                                }
                            });
                        });
                        
                        /*
                         * Updates the GUI when a rating filter selection is
                         * made from any of the checkboxes.
                         *
                         * @author Patrice Boulet
                         */
                        function updateRatingFilters(event) {
                            var filter_sel_array =  event.target.value.split("=");
                            var filter_sel_type = filter_sel_array[0];
                            var filter_sel_value =  parseInt(filter_sel_array[1]);
                            var filter_sel_checked = event.target.checked;
                            
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/RestaurantsController.php',
                                data: {rating_filter: filter_sel_type, 
                                       rating_filter_value: filter_sel_value,
                                       rating_filter_checked: filter_sel_checked},
                                success: function (data) {
                                    var enableClearSearchOptions = false;

                                    $.each($("input[id*='filter']"), function(key, filterInput) {
                                        if( filterInput.checked == true){
                                            enableClearSearchOptions = true;
                                        }
                                    });
                                    
                                    updateLocationListHtmlElements(data, !enableClearSearchOptions, false, false);
                                }
                            });
                        }
                        
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
                                    
                                    // clear all rating filters input checkboxes
                                    $.each($( "input[id*='filter']" ), function(){
                                        this.checked = false;
                                    })
                                }
                            });
                        }
                        
                        /*
                         * Updates the locations list element in the GUI.
                         *
                         * @author Patrice Boulet
                         */
                        function updateLocationListHtmlElements(response, disableClearSearchOptions, updateTypesMenuAndTags, updateSortingTag){
                            //console.log(response);
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
                        
                        /*
                         * Deletes a location and updates GUI after it's done.
                         *
                         * @author Patrice Boulet
                         */
                        function deleteLocation(locationId){
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/RestaurantsController.php',
                                data: 'delete_location=' + locationId,
                                success: function (response) {
                                    var responseArray = $.parseJSON(response);
                                    updateLocationListHtmlElements(response, responseArray[4], false, false);
                                }
                            });
                        }
                            
                        /*
                         * Updates the checked status of the rating filters inputs.
                         *
                         * @author Patrice Boulet
                         */
                        function updateRatingFiltersInputState() {
                            <?php 
                                if (isset($_SESSION['avg_global_r_filter'])){
                                    foreach($_SESSION['avg_global_r_filter'] as $filter_val){ ?>
                                        $('#filterGlobal' + <?php echo $filter_val ?>)[0].checked = true;
                                <?php }
                                }?>
                            <?php 
                                if (isset($_SESSION['avg_food_r_filter'])){
                                    foreach($_SESSION['avg_food_r_filter'] as $filter_val){ ?>
                                        $('#filterFood' + <?php echo $filter_val ?>)[0].checked = true;
                                <?php }
                                }?>
                            <?php 
                                if (isset($_SESSION['avg_service_r_filter'])){
                                    foreach($_SESSION['avg_service_r_filter'] as $filter_val){ ?>
                                        $('#filterService' + <?php echo $filter_val ?>)[0].checked = true;
                                <?php }
                                }?>
                            <?php 
                                if (isset($_SESSION['avg_ambiance_r_filter'])){
                                    foreach($_SESSION['avg_ambiance_r_filter'] as $filter_val){ ?>
                                        $('#filterAmbiance' + <?php echo $filter_val ?>)[0].checked = true;
                                <?php }
                                }?>
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