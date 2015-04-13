<!--
  * Raters page.
  *
  * @author Junyi Dai
  */
-->

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
    <link href="../../../framwork_dir/bootstrap/css/bootstrap.css" rel="stylesheet">

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
    <script src="../../../public_html/protected_private/js/AddLocation.js"></script>
    
    
    <?php 
        // controller 
        require_once(dirname(dirname(__FILE__)) . '\controllers\RatersController.php');
        
        // modal dialogs 
        include('/SignupModal.html'); 
        include('/LoginModal.html'); 
    ?>
    
    <?php
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
                        <a href="raters.php">Raters</a>
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
                    <span style="font-size:26pt; font-weight: bold; color:white;">Raters</span>
                </div>
            </div>
            <div class="col-md-12">
                

            <div class="col-md-12">
                <div class="row">

                <div class="row">
                <div class="col-sm-3">
                 <select id="types_select" class="btn btn-default dropdown-toggle" onchange="updateSorting(this.value)">    
                                <option value="" disabled selected>Sort by...</option>
                                <option value='total_num_ratings ASC'>Number of Ratings (ascending)</option>
                                <option value='total_num_ratings DESC'>Number of Ratings (descending)</option>

                                <option value='join_date ASC'>Join Date (ascending)</option>
                                <option value='join_date DESC'>Join Date (descending)</option>

                                <!-- <option value='rater_id ASC'>Rater ID (ascending)</option>
                                <option value='rater_id DESC'>Rater ID (descending)</option> -->

                                <option value='_name ASC'>Username (ascending)</option>
                                <option value='_name DESC'>Username (descending)</option>

                                <option value='rater_reputation ASC'>Rater Reputation (ascending)</option>
                                <option value='rater_reputation DESC'>Rater Reputation (descending)</option>

                                <option value='_type'>Type (ascending)</option>


                        </select>
                        <button id="clear_search_options_btn" type="button" class="btn btn-default" 
                                onclick="clearAllSearchOptions()" 
                                    <?php if(!isset($_SESSION['all_raters_sorting']))        echo 'disabled'; ?>> Clear search options</button>
                </div>
                    <div class="col-sm-9 col-lg-9 col-md-9">

                        <div id="raters_list" class="list-group">
                        <?php echo get_raters_html_items($raters_list) ?>
                        </div>
                        <script type="text/javascript">
                        /*
                         * Updates GUI when a sorting option is selected from 
                         * the dropdown menu.
                         *
                         * @author Junyi Dai
                         */
                        function updateSorting(selected){
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/RatersController.php',
                                data: {all_raters_sorting: selected},
                                success: function (data) {
                                    console.log(data);
                                    console.log(JSON.parse(data));
                                    updateRatersListHtmlElements(data, false);
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
                            $.ajax({
                                type: 'POST',
                                url: '../controllers/RatersController.php',
                               // data: 'clear_all_search_options=' + 'true',
                                data: {all_raters_sorting: true, clear_all_search_options : true},
                                success: function (data) {
                                    console.log(data);
                                    console.log(JSON.parse(data));
                                    updateRatersListHtmlElements(data, true)
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
                        function updateRatersListHtmlElements(response, disableClearSearchOptions){
                            var html_response = $.parseJSON(response);
                            //var response = response;
                            //update ratings lists
                            console.log(html_response);
                            $('#raters_list').html(html_response);
                            // disable the clear search options button
                            $('#clear_search_options_btn').prop('disabled', disableClearSearchOptions);
                        }
</script>




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