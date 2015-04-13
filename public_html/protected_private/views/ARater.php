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
                        <a onclick="myProfile()">My Profile</a>
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
                                <option value='_name'>Restaurant </option>
                                <option value='rating_id ASC'>Rating ID (ascending) </option>
                                <option value='rating_id DESC'>Rating ID (descending) </option>
                                <option value='l.location_id ASC'>Location ID (ascending) </option>
                                <option value='l.location_id DESC'>Location ID (descending) </option>
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
                <li class="list-group-item text-right"><span class="pull-left"><strong class="">Email: </strong></span>  <?php 	echo $rater->email ?></li>
                <li class="list-group-item text-right"><span class="pull-left"><strong class="">Rater ID: </strong></span>  <?php 	echo $rater->rater_id ?></li>
                <li class="list-group-item text-right"><span class="pull-left"><strong class="">Joined: </strong></span>  <?php 	echo $rater->join_date ?></li>
                <li class="list-group-item text-right"><span class="pull-left"><strong class="">Last Rating Date: </strong></span> <?php 	echo $last_rated ?></li>
                <li class="list-group-item text-right"><span class="pull-left"><strong class="">Type: </strong></span><?php 	echo $rater->_type ?></li>
<!-- 				<?php 	echo deleteButton() ?>
				 -->
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
    					console.log(data);
    			        alert("Account successfully deleted");
    			        window.location.href = "rater.php";
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

        <script src="/plugins/bootstrap-select.min.js"></script>
        <script src="/codemirror/jquery.codemirror.js"></script>
        <script src="/beautifier.js"></script>
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
          ga('create', 'UA-40413119-1', 'bootply.com');
          ga('send', 'pageview');
        </script>
        <script>
        jQuery.fn.shake = function(intShakes, intDistance, intDuration, foreColor) {
            this.each(function() {
                if (foreColor && foreColor!="null") {
                    $(this).css("color",foreColor); 
                }
                $(this).css("position","relative"); 
                for (var x=1; x<=intShakes; x++) {
                $(this).animate({left:(intDistance*-1)}, (((intDuration/intShakes)/4)))
                .animate({left:intDistance}, ((intDuration/intShakes)/2))
                .animate({left:0}, (((intDuration/intShakes)/4)));
                $(this).css("color",""); 
            }
          });
        return this;
        };
        </script>
        <script>
        $(document).ready(function() {
        
            $('.tw-btn').fadeIn(3000);
            $('.alert').delay(5000).fadeOut(1500);
            
            $('#btnLogin').click(function(){
                $(this).text("...");
                $.ajax({
                    url: "/loginajax",
                    type: "post",
                    data: $('#formLogin').serialize(),
                    success: function (data) {
                        //console.log('data:'+data);
                        if (data.status==1&&data.user) { //logged in
                            $('#menuLogin').hide();
                            $('#lblUsername').text(data.user.username);
                            $('#menuUser').show();
                            /*
                            $('#completeLoginModal').modal('show');
                            $('#btnYes').click(function() {
                                window.location.href="/";
                            });
                            */
                        }
                        else {
                            $('#btnLogin').text("Login");
                            prependAlert("#spacer",data.error);
                            $('#btnLogin').shake(4,6,700,'#CC2222');
                            $('#username').focus();
                        }
                    },
                    error: function (e) {
                        $('#btnLogin').text("Login");
                        console.log('error:'+JSON.stringify(e));
                    }
                });
            });
            $('#btnRegister').click(function(){
                $(this).text("Wait..");
                $.ajax({
                    url: "/signup?format=json",
                    type: "post",
                    data: $('#formRegister').serialize(),
                    success: function (data) {
                        console.log('data:'+JSON.stringify(data));
                        if (data.status==1) {
                            $('#btnRegister').attr("disabled","disabled");
                            $('#formRegister').text('Thanks. You can now login using the Login form.');
                        }
                        else {
                            prependAlert("#spacer",data.error);
                            $('#btnRegister').shake(4,6,700,'#CC2222');
                            $('#btnRegister').text("Sign Up");
                            $('#inputEmail').focus();
                        }
                    },
                    error: function (e) {
                        $('#btnRegister').text("Sign Up");
                        console.log('error:'+e);
                    }
                });
            });
            
            $('.loginFirst').click(function(){
                $('#navLogin').trigger('click');
                return false;
            });
            
            $('#btnForgotPassword').on('click',function(){
                $.ajax({
                    url: "/resetPassword",
                    type: "post",
                    data: $('#formForgotPassword').serializeObject(),
                    success: function (data) {
                        if (data.status==1){
                            prependAlert("#spacer",data.msg);
                            return true;
                        }
                        else {
                            prependAlert("#spacer","Your password could not be reset.");
                            return false;
                        }
                    },
                    error: function (e) {
                        console.log('error:'+e);
                    }
                });
            });
            
            $('#btnContact').click(function(){
                
                $.ajax({
                    url: "/contact",
                    type: "post",
                    data: $('#formContact').serializeObject(),
                    success: function (data) {
                        if (data.status==1){
                            prependAlert("#spacer","Thanks. We got your message and will get back to you shortly.");
                             $('#contactModal').modal('hide');
                            return true;
                        }
                        else {
                            prependAlert("#spacer",data.error);
                            return false;
                        }
                    },
                    error: function (e) {
                        console.log('error:'+e);
                    }
                });
                return false;
            });
            
            /*
            $('.nav .dropdown-menu input').on('click touchstart',function(e) {
                e.stopPropagation();
            });
            */
            
            
            
            
            
        });
        $.fn.serializeObject = function()
        {
            var o = {};
            var a = this.serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        };
        var prependAlert = function(appendSelector,msg){
            $(appendSelector).after('<div class="alert alert-info alert-block affix" id="msgBox" style="z-index:1300;margin:14px!important;">'+msg+'</div>');
            $('.alert').delay(3500).fadeOut(1000);
        }
        </script>
        <!-- Quantcast Tag -->
        <script type="text/javascript">
        var _qevents = _qevents || [];
        
        (function() {
        var elem = document.createElement('script');
        elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
        elem.async = true;
        elem.type = "text/javascript";
        var scpt = document.getElementsByTagName('script')[0];
        scpt.parentNode.insertBefore(elem, scpt);
        })();
        
        _qevents.push({
        qacct:"p-0cXb7ATGU9nz5"
        });
        </script>
        <noscript>
        &amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;lt;div style="display:none;"&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;gt;
        &amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;lt;img src="//pixel.quantserve.com/pixel/p-0cXb7ATGU9nz5.gif" border="0" height="1" width="1" alt="Quantcast"/&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;gt;
        &amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;lt;/div&amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;gt;
        </noscript>
        <!-- End Quantcast tag -->
        <div id="completeLoginModal" class="modal hide">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close">×</a>
                 <h3>Do you want to proceed?</h3>
            </div>
            <div class="modal-body">
                <p>This page must be refreshed to complete your login.</p>
                <p>You will lose any unsaved work once the page is refreshed.</p>
                <br><br>
                <p>Click "No" to cancel the login process.</p>
                <p>Click "Yes" to continue...</p>
            </div>
            <div class="modal-footer">
              <a href="#" id="btnYes" class="btn danger">Yes, complete login</a>
              <a href="#" data-dismiss="modal" aria-hidden="true" class="btn secondary">No</a>
            </div>
        </div>
        <div id="forgotPasswordModal" class="modal hide">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close">×</a>
                 <h3>Password Lookup</h3>
            </div>
            <div class="modal-body">
                  <form class="form form-horizontal" id="formForgotPassword">    
                  <div class="control-group">
                      <label class="control-label" for="inputEmail">Email</label>
                      <div class="controls">
                          <input name="_csrf" id="token" value="CkMEALL0JBMf5KSrOvu9izzMXCXtFQ/Hs6QUY=" type="hidden">
                          <input name="email" id="inputEmail" placeholder="you@youremail.com" required="" type="email">
                          <span class="help-block"><small>Enter the email address you used to sign-up.</small></span>
                      </div>
                  </div>
                  </form>
            </div>
            <div class="modal-footer pull-center">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn">Cancel</a>  
                <a href="#" data-dismiss="modal" id="btnForgotPassword" class="btn btn-success">Reset Password</a>
            </div>
            
        </div>
        <div id="upgradeModal" class="modal hide">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close">×</a>
                 <h4>Would you like to upgrade?</h4>
            </div>
            <div class="modal-body">
                  <p class="text-center"><strong></strong></p>
                  <h1 class="text-center">$4<small>/mo</small></h1>
                  <p class="text-center"><small>Unlimited plys. Unlimited downloads. No Ads.</small></p>
                  <p class="text-center"><img src="/assets/i_visa.png" alt="visa" width="50"> <img src="/assets/i_mc.png" alt="mastercard" width="50"> <img src="/assets/i_amex.png" alt="amex" width="50"> <img src="/assets/i_discover.png" alt="discover" width="50"> <img src="/assets/i_paypal.png" alt="paypal" width="50"></p>
            </div>
            <div class="modal-footer pull-center">
                <a href="/upgrade" class="btn btn-block btn-huge btn-success"><strong>Upgrade Now</strong></a>
                <a href="#" data-dismiss="modal" class="btn btn-block btn-huge">No Thanks, Maybe Later</a>
            </div>
        </div>
        <div id="contactModal" class="modal hide">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close">×</a>
                <h3>Contact Us</h3>
                <p>suggestions, questions or feedback</p>
            </div>
            <div class="modal-body">
                  <form class="form form-horizontal" id="formContact">
                      <input name="_csrf" id="token" value="CkMEALL0JBMf5KSrOvu9izzMXCXtFQ/Hs6QUY=" type="hidden">
                      <div class="control-group">
                          <label class="control-label" for="inputSender">Name</label>
                          <div class="controls">
                              <input name="sender" id="inputSender" class="input-large" placeholder="Your name" type="text">
                          </div>
                      </div>
                      <div class="control-group">
                          <label class="control-label" for="inputMessage">Message</label>
                          <div class="controls">
                              <textarea name="notes" rows="5" id="inputMessage" class="input-large" placeholder="Type your message here"></textarea>
                          </div>
                      </div>
                      <div class="control-group">
                          <label class="control-label" for="inputEmail">Email</label>
                          <div class="controls">
                              <input name="email" id="inputEmail" class="input-large" placeholder="you@youremail.com (for reply)" required="" type="text">
                          </div>
                      </div>
                  </form>
            </div>
            <div class="modal-footer pull-center">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn">Cancel</a>     
                <a href="#" data-dismiss="modal" aria-hidden="true" id="btnContact" role="button" class="btn btn-success">Send</a>
            </div>
        </div>
        
        
        
    
    <script src="/plugins/bootstrap-pager.js"></script>
</div>
 </body>





</html>