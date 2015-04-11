/*
  * Add a location component logic.
  *
  * @author Patrice Boulet
  */


//  executed on the document load event
$(function (){
  
    $('#addLocationFormSubmit').click(function(){
        validateForm("addLocationForm");
    });

});

function addLocation(){
    checkLoggedIn().success(function(logged_in){
        if(logged_in){
            $.ajax({
                  type: "POST",
                  url: "../controllers/AddLocationModalController.php",
                  data: {get_all_restaurants:true},
                  success: function(response){
                        var responseArray = JSON.parse(response);
                        var restaurantsAvailOptions = responseArray[0];
                        var restaurantTypesAvailOptions = responseArray[1];
                        
                        $('#addLocationRestaurantsAvail').html(restaurantsAvailOptions);
                        $('#addLocationRestaurantTypesAvail').html(restaurantTypesAvailOptions);
                        
                      
                        $('#addLocationRestaurantTypesAvail').multiselect({
                            enableFiltering: true
                        });
                      
                        $('#addLocationModal').modal('show');// triggers login modal to display
                   }
            });
        }else{
            showLoginModal();
        }
    });
}