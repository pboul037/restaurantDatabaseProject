    /*
     * Shows the signup form.
     *
     * @author Patrice Boulet
     */
    function showSignupModal(){
        $.ajax({
              type: "POST",
              url: "../controllers/SignupModalController.php",
              data: {rater_types:true},
              success: function(html_rater_types){
                    $('#signupRaterType').html(html_rater_types);
                    $('#signupModal').modal('show');// triggers signup modal to display
               }
        });
    }

    /*
     * Shows the login form.
     *
     * @author Patrice Boulet
     */
    function showLoginModal(){
        $('#loginModal').modal('show');// triggers login modal to display
    }
    
    /*
     * Check if logged in.
     *
     * @author Patrice Boulet
     */
    function checkLoggedIn(){
        return $.ajax({
              type: "POST",
              url: "../controllers/SessionController.php",
              data: {check_logged_in:true}
        });
    }

    /*
     * Logs out a user.
     *
     * @author Patrice Boulet
     */
    function logout(){
        $.ajax({
            type: "POST",
            url: "../controllers/SessionController.php",
            data: {logout:true}//,
            //success: function() {
            //    $.notify("Logged out", "error");
            //}
        });
        $('#sessionButtons').html('<li><a id="loginBtn" style="cursor: pointer">Login</a>' +
                                    '</li>' + 
                                  '<li><!-- Button trigger modal --> ' +
                                  '<a id="signUpBtn" style="cursor: pointer">Sign up</a></li>');
    }

    // executed on the document ready event
    $( function() {

        $('#signupModal').on('shown.bs.modal', function () {
          $('#signupEmail').focus()
        });
        $('#loginModal').on('shown.bs.modal', function () {
          $('#loginUsername').focus()
        });
        
        // activates the session button actions on the top right
        $(document).on('click', '#loginBtn', showLoginModal);
        $(document).on('click', '#signUpBtn', showSignupModal);
        $(document).on('click', '#logoutBtn', logout);
        //$('#usernameBtn'),on('click' showUserProfile);

        /*
         * Handles submission and validation of the signup form.
         *
         * @author Patrice Boulet
         */
        $('#signUpFormSubmit').on('click', function(){
            var validationJSONresult = validateForm("signupForm");
            if(validationJSONresult != false){
                $.ajax({
                  type: "POST",
                  url: "../controllers/SignupModalController.php",
                  data: "signup_form_data=" + validationJSONresult,
                  success: function(html_rater_types){
                    $('#signupModal').modal('hide');
                   }
                });
            }
        });

        /*
         * Handles submission and validation of the login form.
         *
         * @author Patrice Boulet
         */
        $('#loginFormSubmit').on('click', function(){
            var validationResult = validateForm("loginForm");
        });
    });