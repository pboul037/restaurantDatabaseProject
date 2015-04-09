//  executed on the document load event
$(function (){
  
    $('#addLocationRatingFormSubmit').click(function(){
        validateForm("addLocationRatingForm");
    });

});

function addRating(){
    checkLoggedIn().success(function(logged_in){
        if(logged_in){
            $('#addLocationRatingModal').modal('show');// triggers login modal to display
        }else{
            showLoginModal();
        }
    });
}