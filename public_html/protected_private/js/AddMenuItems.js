/*
  * Add menu item component logic.
  *
  * @author Patrice Boulet
  */

//  executed on the document load event
$(function (){
  
    $('#addLocationMenuItemFormSubmit').click(function(){
        validateForm("addLocationMenuItemForm");
    });
    
    $('.btn-default.menuItemType').click(function () {
        
        var typeSel = $(this).children()[0].value;
        $("#menuItemTypeButtonvalue").val(typeSel);
        
        $.ajax({
          type: "POST",
          url: "../controllers/AddMenuItemModalController.php",
          data: {get_categories:typeSel},
          success: function(response){
                var categoriesArray = JSON.parse(response);
                
                var categoryOptionsHtml = "<option value='' disabled selected>Select Category</option>";
              
                categoriesArray.forEach(function(category){
                    categoryOptionsHtml += '<option value="' + category  + '">' + category  + '</option>';
                });
              
                $('#addMenuItemCategoriesAvail').html(categoryOptionsHtml);       
           }
        });
    });
});

function addMenuItem(){
    checkLoggedIn().success(function(logged_in){
        if(logged_in){
            $('#addLocationMenuItemModal').modal('show');// triggers login modal to display
        }else{
            showLoginModal();
        }
    });
}