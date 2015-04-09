
<?php
session_start();



    // include configuration
    require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');
    
    $dal =  new DAL();
    //QA
    //Declaring the list that is to hold on to the results of the SQL quesry for a list of locations
    $location_ratings_list;
    $new_locations_ratings_list_html = "";
    
    //QA
    //Declaring the variable that will hold on to the location name and information associated with the location
    //that matches the given location id

    $location_id;
    if(!isset($_POST['locations_ratings_sorting_selected']))
    {
        $details = $dal->get_location_details($_GET['locationid'], null);
        $location_id = $_GET['locationid'];
    }
    else
    {
        $details = $dal->get_location_ratings($_POST['location_id'], $_POST['locations_ratings_sorting_selected']);
        $location_id = $_POST['location_id'];
    }
    


    // to solve this error: we can get the location_id when dynamically selecting a sorting parameter with ($_POST['location_id'])
    
    //We are only interested in the first entry of the array, because it corresponds to the first and only row of the
    //corresponding sql query result table.
    $details = $details[0];
    
    
    //QA
    //Declaring variable that holds on to the gmail adress information for this location
    $address_for_gmaps_splitted = explode( ",", $details->street_address );
    $address_for_gmaps = join('+', $address_for_gmaps_splitted);
    
    //QA
    //Declaring variable that holds on to the opening hours for the given location
    $opening_hours = explode(",", $details->opening_hours);
    
    //QA
    //Declaring variables that will hold on to the list of appetizers, mains, and desserts, respecfully of the given location
    $appetizers_html = get_menu_items('food', 'appetizer');
    $mains_html = get_menu_items('food', 'main');
    $desserts_html = get_menu_items('food', 'dessert');
    
    //QA
    //Declaring the variable that will hold on to the categories of beverages for the given location
    $beverage_categories = $dal->get_beverages_categories($details->location_id);
    
    //QA
    //QUESTION: I do not know what we are declaring here
    $beverages_menu_items_by_category = array();
    
    //QA
    //QUESTION what are we doing here? why is beverages treated specially compared to other menu items 
    //ANSWER: b/c its like in a restaurant menu, the drink and food menus are separated -PB
    foreach($beverage_categories as $category){
    $beverages_menu_items_by_category[$category->category] = get_menu_items('drink', $category->category);
    }
    get_location_rating();
    
    
    //QA
    //Calling the methods that ar going to be used to update the location's ratings
    function get_location_rating(){
        global $dal, $location_ratings_list,$new_locations_ratings_list_html, $location_id;
        $is_clear_search_options = isset($_POST['clear_all_search_options']);
        $response = array();


        //QA
        //Declaring the variable that will hold the list of all the ratings corresponding to a given location
        $location_ratings_list;

        //QA
        //Declaring a variable that will hold on to the type of sorting selected
        $typeof_sorting_selected;

        //QA
        //Declaring a boolean to know if we should clear the search options
        //$clear_search_options = isset($_POST['clear_all_search_options']);

        //QA
        //Declaring a boolean to know if a sorting was selected
        $is_sorting_selected = isset($_POST['locations_ratings_sorting_selected']);


        //QA
        if($is_sorting_selected && !$is_clear_search_options){
        //we must save this new option in the session so we can remember
        //selected options for refresh ect.
            $_SESSION['locations_ratings_sorting_selected'] = $_POST['locations_ratings_sorting_selected'];
            $typeof_sorting_selected = $_SESSION['locations_ratings_sorting_selected'];
        }
        else if (!$is_sorting_selected && !$is_clear_search_options){
            if(isset($_SESSION['locations_ratings_sorting_selected']))
                $typeof_sorting_selected = $_SESSION['locations_ratings_sorting_selected'];
            else
                $typeof_sorting_selected = null;
        }
        else if($is_clear_search_options){
            $typeof_sorting_selected = null;
            unset($_SESSION['locations_ratings_sorting_selected']);
        }



       // //QA
       //Calls dal to get the list of ratings for this location, with the applied sorting option
       //If there is no sorting option selected then $typeof_sorting_selected = null
       $location_ratings_list = $dal->get_location_ratings($location_id, $typeof_sorting_selected);
       //$new = get_location_rating_html_items($location_ratings_list);
    

       //QA
       //Calling the method that will return the html code to display the new list of ratings
       $new_locations_ratings_list_html .= get_location_rating_html_items($location_ratings_list);
//echo 'hi';

        if($is_sorting_selected){
            //prepare array containing response
            array_push($response, $new_locations_ratings_list_html);

            // send the response
            echo json_encode($response);
        } 
       
    }



    /*
     *QA
     *This method gets called by the get_location_rating() and the purpose of this method is to take a list
     *of ratings and generate the proper html to display those ratings
     *
     * Returns a $location_rating list html item as 
     * a string.
     *
     * @author Junyi Dai, Qasim Ahmed
     */
    function get_location_rating_html_items($location_ratings_list){

        //QA
        //Declaring the variable that will hold all the HTML code that will be returned to the view
        $location_rating_html_item = '';

        //JD: Declaring the variable that will hold the dollar sign string to store the price rating (out of 5)
        $dollar_sign_string = '';

       //QA
       //Loops through the the list of ratings and generates html for each one to display on the view
        foreach($location_ratings_list as $location_rating){

            // add gold $ for actual price avg and store it in $dollar_sign_string
            for( $i = 0; $i < $location_rating->price; $i++){
                $dollar_sign_string .= '<h6 <span class="glyphicon glyphicon-usd" style="color:black"></span></h6>';
            }

            // add the subtraction of 5 by avg price of grey $ and store it in $dollar_sign_string
            for( $i = 0; $i < 5-$location_rating->price; $i++){
                $dollar_sign_string .= '<h6 <span class="glyphicon glyphicon-usd" style="color:#DCDCDC"></span></h6>';
            }

            $location_rating_html_item .= 
            '<div class="list-group-item"><div class="row">
            <div class="col-sm-4">
                <div class="row">
                    <span class="col-sm-12">
                        <span style="font-size:14pt; font-style:italic;">Food: </span>
                        <span style="font-size:12pt; font-weight:bold;">' . $location_rating->food . '</span>
                        <span style="font-size:12pt">out of 5 </span>
                    </span>
                </div>
                <div class="row">
                    <span class="col-sm-12">
                        <span style="font-size:14pt; font-style:italic;">Ambiance: </span>
                        <span style="font-size:12pt; font-weight:bold;">' . $location_rating->ambiance . '</span>
                        <span style="font-size:12pt">out of 5 </span>
                    </span>
                </div>
                <div class="row">
                    <span class="col-sm-12">
                        <span style="font-size:14pt; font-style:italic;">Service: </span>
                        <span style="font-size:12pt; font-weight:bold;">' . $location_rating->service . '</span>
                        <span style="font-size:12pt">out of 5 </span>
                    </span>
                </div>
                <div class="row">
                    <span class="col-sm-12">
                        <span style="font-size:14pt; font-style:italic;">Price: </span>
                        <span style="font-size:12pt; font-weight:bold;">' . $dollar_sign_string . '</span>
                    </span>
                </div>
            </div>
            <div class="col-sm-12">
                <span style="font-size:10pt">By </span>
                <a style="font-size:10pt" href="Location.php?locationid=' . $location_rating->location_id . '#ratings">' 
                    . $location_rating->_name .'</a>
                    <span style="font-size:10pt">on </span>
                    <span style="font-size:10pt;">' . $location_rating->date_written . '</span>
                </div>
                <div class="col-sm-12
                <span style="font-size:10pt;">' . $location_rating->_comments . '</span>
            </div>
            </div>
            </div>';

            // Clear the dollar sign string
            $dollar_sign_string = '';
    }
    return $location_rating_html_item;
    }


        /* 
         * Returns a list of html list element for 
         * menu items that are of $type type and $category category.
         *
         * @author Patrice Boulet
         */
    function get_menu_items($type, $category){
        global $dal, $details;
        
        $menu_items_html = "";
        $menu_items = $dal->get_menu_items($details->location_id, $type, $category);
        $menu_items_html .= '<div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" 
          href="#collapse' . $category . '">' . $category . '</a><span class="pull-right badge">' . count($menu_items) . '</span>
        </h4>
        </div>
        <div id="collapse' . $category . '" class="panel-collapse collapse">
        <ul class="list-group">
        <?php echo $appetizers_html; ?>';
        
        foreach($menu_items as $menu_item){
            $menu_items_html .= '<li class="list-group-item"><div class="pull-right"><h5>$ ' . $menu_item->price . '</h5></div><h5>' .                              $menu_item->_name . '</br>
            <small>' . $menu_item->description . '</small></h5><span class="badge"></span></li>
            ';
        }
        
        $menu_items_html .= '</ul></div>';
        
        return $menu_items_html;
    }   
        ?>