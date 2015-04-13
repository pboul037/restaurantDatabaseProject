/*
  * Add a rating component logic.
  *
  * @author Patrice Boulet
  */


//  executed on the document load event
$(function (){
  
    $('#addLocationRatingFormSubmit').click(function(){
        validateForm("addLocationRatingForm");
    });

});

function addRating(){
    checkLoggedIn().success(function(logged_in){
        if(logged_in){
            $.ajax({
                  type: "POST",
                  url: "../controllers/AddRatingModalController.php",
                  data: {add_rating:((window.location.search).split('='))[1]},
                  success: function(response){
                        var responseArray = JSON.parse(response);
                        
                        var drinksAvail = responseArray[0];
                        var foodAvail = responseArray[1];
                      
                        $('#addRatingDrinksAvail').html(drinksAvail);
                        $('#addRatingFoodAvail').html(foodAvail);
                        $('#addRatingDrinksAvail').multiselect({
                            enableFiltering: true
                        });
                        $('#addRatingFoodAvail').multiselect({
                            enableFiltering: true
                        });
                      
                      
                        $('#addLocationRatingModal').modal('show');// triggers login modal to display
                   }
            });
        }else{
            showLoginModal();
        }
    });
}