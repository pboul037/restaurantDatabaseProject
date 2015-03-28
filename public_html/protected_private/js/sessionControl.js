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
        $('#sessionButtons').html('<li><a style="cursor: pointer" onclick="showLoginModal">Login</a>' +
                                    '</li>' + 
                                  '<li><!-- Button trigger modal --> ' +
                                  '<a style="cursor: pointer" onclick="showSignupModal">Sign up</a></li>');
    }

    // executed on the document ready event
    $( function() {

        $('#signupModal').on('shown.bs.modal', function () {
          $('#signupEmail').focus()
        });
        $('#loginModal').on('shown.bs.modal', function () {
          $('#loginUsername').focus()
        });

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