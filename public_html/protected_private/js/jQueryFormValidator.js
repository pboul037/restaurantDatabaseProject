/**
  * A small jQuery form validation function for the signup and login forms.
  *
  * @author Patrice Boulet
  */
function validateForm(formId){

    // initialize the user feedback
    $('.has-error').removeClass('has-error');
    $('.has-success').removeClass('has-error');
    $('span.validationError').remove();
    $('br.validationError').remove();
    $('span.validationSuccess').remove();
        
    // validate the signup form
    if(formId == "signupForm"){
    
        var usernameReg = /^[A-Za-z0-9]+$/;
        var numberReg =  /^[0-9]+$/;
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        var email = $('#signupEmail').val();
        var username = $('#signupUsername').val();
        var pswd = $('#signupPassword').val();
        var pswdConfirm = $('#signupPasswordConfirm').val();
        var rater_type = $('#signupRaterType option:selected').text();
        
        var inputVal = new Array(email, username, pswd, pswdConfirm, rater_type);

            if(inputVal[0] == ""){
                $('#signupEmailGroup').addClass("has-error has-feedback");
                $('#signupEmail').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><span  class="validationError"> Please enter your email.</span>');
            } 
            else if(!emailReg.test(email)){
                $('#signupEmailGroup').addClass("has-error has-feedback");
                $('#signupEmail').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><span  class="validationError"> Please enter a valid email.</span>');
            }else {
                $('#signupEmailGroup').addClass("has-success has-feedback");
                $('#signupEmail').after('<span class="validationSuccess glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
            }

            if(inputVal[1] == ""){
                $('#signupUsernameGroup').addClass("has-error has-feedback");
                $('#signupUsername').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><span  class="validationError"> Please enter a username.</span>');
            } else {
                $('#signupUsernameGroup').addClass("has-success has-feedback");
                $('#signupUsername').after('<span class="validationSuccess glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
            }

            if(inputVal[2] == ""){
                $('#signupPasswordGroup').addClass("has-error has-feedback");
                $('#signupPassword').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><span  class="validationError"> Please enter a password.</span>');
            } else {
                $('#signupPasswordGroup').addClass("has-success has-feedback");
                $('#signupPassword').after('<span class="validationSuccess glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
            }

            if(inputVal[3] == ""){
                $('#signupPasswordConfirmGroup').addClass("has-error has-feedback");
                $('#signupPasswordConfirm').after('<span class="validationError" glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><span  class="validationError"> Please confirm your password.</span>');
            } else if (inputVal[2] != inputVal[3]){
                $('#signupPasswordConfirmGroup').addClass("has-error has-feedback");
                $('#signupPasswordConfirm').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><span  class="validationError"> Passwords do no match.</span>');
            }else {
                $('#signupPasswordConfirmGroup').addClass("has-success has-feedback");
                $('#signupPasswordConfirm').after('<span class="validationSuccces glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
            }

            if(inputVal[4] == "" || inputVal[4] == "Choose Rater Type..."){
                $('#signupRaterTypeGroup').addClass("has-error has-feedback");
                $('#signupRaterType').after('</br><span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><span class="validationError">Please select a rater type.</span>');
            } else {
                $('#signupRaterTypeGroup').addClass("has-success has-feedback");
                $('#signupRaterType').after('<span class="validationSuccess glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
            }
        if( $('span.validationError').length == 0)
            return JSON.stringify(inputVal);
        else
            return false;
    
    // validates the login form
    }else if (formId == "loginForm"){
    
        var username = $('#loginUsername').val();
        var pswd = $('#loginPassword').val();
        
        var inputVal = new Array(username, pswd);
        
            // update user feedback
            if(inputVal[0] == ""){
                $('#loginUsernameGroup').addClass("has-error has-feedback");
                $('#loginUsername').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><span  class="validationError"> Please enter a username.</span>');
            } 

            if(inputVal[1] == ""){
                $('#loginPasswordGroup').addClass("has-error has-feedback");
                $('#loginPassword').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><span  class="validationError"> Please enter a password.</span>');
            }
        
        // when there are error in form data entries
        if( $('span.validationError').length == 0){
            $.ajax({
              type: "POST",
              url: "../controllers/LoginModalController.php",
              data: "login_form_data=" + JSON.stringify(inputVal),
              success: function(matchedCredentials){
                if(matchedCredentials > 0){ // login was successful
                    
                    // update user feedback
                    $('#loginUsernameGroup').addClass("has-success has-feedback");
                    $('#loginUsername').after('<span class="validationSuccess glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
                    $('#loginPasswordGroup').addClass("has-success has-feedback");
                    $('#loginPassword').after('<span class="validationSuccess glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
                    
                    $('#sessionButtons').html('<li><a style="cursor: pointer" onclick="">' +
                                              $('#loginUsername').val() + '</a></li>' +
                                              '<li><a style="cursor: pointer" onclick="logout()">Log out</a></li>');
                    
                    $('#loginModal').modal('hide');
                    $.notify("Logged in", "success");
                }else{ // login failed
                    
                    // update user feedback
                    $('#loginUsernameGroup').addClass("has-error has-feedback");
                    $('#loginUsername').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>');
                    $('#loginPasswordGroup').addClass("has-error has-feedback");
                    $('#loginPassword').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><span  class="validationError"> Login failed : Invalid username or password.</span>');
                }
               }
            });
            return true;
        }else{
            return false;    
        }
        // validate add a location rating form
        }else if( formId == "addLocationRatingForm"){
            var foodRating = $('#addLocationRatingFood').val();
            var serviceRating = $('#addLocationRatingService').val();
            var ambianceRating = $('#addLocationRatingAmbiance').val();
            var price = $('#addLocationRatingPrice').val();
            var comments = $('#addLocationRatingComments').val();
            var location_id = parseInt(location.search.split("=")[1]);
            
            var inputVal = new Array(foodRating, serviceRating, ambianceRating, price, comments, location_id);
            
            if(inputVal[0] == ""){
                $('#addLocationRatingFoodGroup').addClass("has-error has-feedback");
                $('#addLocationRatingFood').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><br class="validationError"><span  class="validationError"> Please rate the food.</span>');
            } else {
                $('#addLocationRatingFoodGroup').addClass("has-success has-feedback");
                $('#addLocationRatingFood').after('<span class="validationSuccess glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
            }
            
            if(inputVal[1] == ""){
                $('#addLocationRatingServiceGroup').addClass("has-error has-feedback");
                $('#addLocationRatingService').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><br class="validationError"><span  class="validationError"> Please rate the service.</span>');
            } else {
                $('#addLocationRatingServiceGroup').addClass("has-success has-feedback");
                $('#addLocationRatingService').after('<span class="validationSuccess glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
            }
            
            if(inputVal[2] == ""){
                $('#addLocationRatingAmbianceGroup').addClass("has-error has-feedback");
                $('#addLocationRatingAmbiance').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><br class="validationError"><span  class="validationError"> Please rate the ambiance.</span>');
            } else {
                $('#addLocationRatingAmbianceGroup').addClass("has-success has-feedback");
                $('#addLocationRatingAmbiance').after('<span class="validationSuccess glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
            }
            
            if(inputVal[3] == ""){
                $('#addLocationRatingPriceGroup').addClass("has-error has-feedback");
                $('#addLocationRatingPrice').after('<span class="validationError glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span><br class="validationError"><span  class="validationError"> Please enter a price range.</span>');
            } else {
                $('#addLocationRatingPriceGroup').addClass("has-success has-feedback");
                $('#addLocationRatingPrice').after('<span class="validationSuccess glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>');
            }
                 // when there are error in form data entries
        if( $('validationError').length == 0){
            $.ajax({
              type: "POST",
              url: "../controllers/AddRatingModalController.php",
              data: "add_location_rating_form_data=" + JSON.stringify(inputVal),
              success: function(){  
                    $.notify("Thanks for rating! The page will now reload in 3 seconds to update your changes...", "success");
                    setTimeout(function(){ 
                        $('#addLocationRatingModal').modal('hide');
                        window.location.hash = "#ratings";
                        location.reload();},3000
                    );
                }
            });
            return true;
        }else{
            return false;    
        }   
        }else
            return false;
    }