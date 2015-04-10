
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

    $menu_item_delete_sel = isset($_POST['delete_menu_item']);            // a menu item's deletion has been requested by admin

    // delete a location
    if($menu_item_delete_sel){      
        $dal->delete_menu_item($_POST['delete_menu_item']);
    }

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
     * @author Patrice Boulet, Junyi Dai, Qasim Ahmed 
     */
    function get_location_rating_html_items($location_ratings_list){
        global $dal;
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
                $dollar_sign_string .= '<span style="font-size:12px" class="glyphicon glyphicon-usd" style="color:black"></span>';
            }

            // add the subtraction of 5 by avg price of grey $ and store it in $dollar_sign_string
            for( $i = 0; $i < 5-$location_rating->price; $i++){
                $dollar_sign_string .= '<span  style="font-size:12px" class="glyphicon glyphicon-usd" style="color:#DCDCDC"></span>';
            }

            $location_rating_html_item .= 
            '<div class="list-group-item">
                <div class="row">
                <div class="col-sm-3">
                    <div class="row"
                            <span style="text-align:center" class="col-sm-5">
                                <h4 style="font-weight:bold">' . $location_rating->avg_rating . '<small style="padding-right:25px"> out of 5</small>' . $dollar_sign_string . '</h4>
                            </span>
                    </div>
                    <div class="row">
                            <span style="text-align:center" class="col-sm-4">
                                <span style="font-size:10pt;">Food </span>
                                <h5 style="font-weight:bold">' . $location_rating->food . '<small>/5</small></h5>
                            </span>
                            <span style="text-align:center" class="col-sm-4">
                                <span style="font-size:10pt;">Ambiance </span>
                                <h5 style="font-weight:bold">' . $location_rating->ambiance . '<small>/5</small></h5>
                            </span>
                            <span style="text-align:center" class="col-sm-4">
                                <span style="font-size:10pt;">Service </span>
                                <h5 style="font-weight:bold">' . $location_rating->service . '<small>/5</small></h5>
                            </span>
                    </div>
                    <span style="font-size:12pt">By </span>
                    <a style="font-size:12pt" href="Location.php?locationid=' . $location_rating->location_id . '#ratings">' 
                        . $location_rating->_name .'
                    </a>
                    <span style="font-size:12pt">on </span>
                    <span style="font-size:10pt;">' . $location_rating->date_written . '</span>
                    <div class="row">
                        <span class="col-sm-1 glyphicon glyphicon-star" style="color:green"></span>
                        <span class="col-sm-1">' . $location_rating->rater_reputation .'</span>
                        <span class="col-sm-5">Reputation</span>
                    </div>
                    <div class="row">
                        <span class="col-sm-1 glyphicon glyphicon-stats" style="color:green"></span>
                        <span class="col-sm-1">' . $location_rating->total_rater_ratings .'</span>
                        <span class="col-sm-5">total ratings</span>
                    </div>
                                       <div class="row">
                        <span class="col-sm-12">Rated this location ' . $location_rating->rater_ratings_for_this_loc .' time(s)</span>
                    </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="well col-sm-12"><span class="col-sm-2">Ordered: </span>';
                        $rating_items_for_rating = $dal->get_rating_items_for_rating($location_rating->rating_id);
                        foreach($rating_items_for_rating as $rating_item){
                            $location_rating_html_item .= '<span class="tagcloud tag label label-warning">' . $rating_item->_name . ' :                                                                 $' . $rating_item->price . '</span>';
                        }
                        if( count($rating_items_for_rating) < 1)
                            $location_rating_html_item .= '<span>No order specified. </span>';

            $location_rating_html_item .= '</div>
                        <div class="col-sm-12 comment">
                            <div class="row">
                                <div class="col-sm-12
                                        <span style="font-size:10pt;">' . 
                                            (strlen($location_rating->_comments) > 0 ? $location_rating->_comments : "No comments") 
                                        . '</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="vertical-align:middle" class="col-sm-1">
                            <div class="row">
                                <a onclick="updateHelpfulness(true, this.dataset.rater_id, this.dataset.ratingid)" style"cursor: pointer" 
                                        data-rater_id="' . $location_rating->rater_id . '" 
                                        data-ratingid="' . $location_rating->rating_id . '"><span style="font-size:16pt; color:green" class="col-sm-12 glyphicon glyphicon-thumbs-up"> </span>
                                <label style="text-align:center; font-size:10pt">Was helpful</label></a>
                            </div>
                            <div class="row">
                                <a onclick="updateHelpfulness(false, this.dataset.rater_id, this.dataset.ratingid)" style"cursor: pointer" 
                                        data-rater_id="' . $location_rating->rater_id . '" 
                                        data-ratingid="' . $location_rating->rating_id . '"><span style="font-size:16pt; color:red" class="col-sm-12 glyphicon glyphicon-thumbs-down"></span>
                                <label style="text-align:center; font-size:10pt">' . "Wasn't helpful</label></a>" . '
                            </div>
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
        $temp_str = "";
        $avg_category_price = null;
        $category_price_sum = 0;
        
        foreach($menu_items as $menu_item){
            $category_price_sum += $menu_item->price;
            $temp_str .= '<li class="list-group-item"  data-itemid="' . $menu_item->item_id . '"><div class="row">';
            
                        // if the admin is logged in, give the privileges to delete a location
            if( isset($_SESSION['username']) ){
                if( $_SESSION['username'] == 'admin'){
                    $temp_str .= '<div class="col-sm-2 pull-left adminControl"><button class="btn btn-danger" onclick="deleteMenuItem(parseInt(this.parentNode.parentNode.parentNode.dataset.itemid))">
                                                <span class="glyphicon glyphicon-trash" style="color:black"> </span>
                                            </button></div>';
                }
            }
            
            $temp_str .= '<div class="pull-right col-sm-3"><h5>$ ' . $menu_item->price;
            
            $temp_str .= '</h5></div><h5 class="col-sm-7">' . $menu_item->_name . '</br>
            <small>' . $menu_item->description . '</small></h5><span class="badge"></span></div></li>
            ';
        }
        if( count($menu_items) >  0)
            $avg_category_price = $category_price_sum/count($menu_items);
        
        $menu_items_html .= '<div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" 
          href="#collapse' . $category . '">' . $category . '</a>';
        
        if( $avg_category_price != null)
            $menu_items_html .= '<span class="pull-right"> (avg. price: $'. round($avg_category_price,2) . ')</span>';
        
        
        $menu_items_html .= '<span style="padding-left:15px"><span class="badge">' . count($menu_items) . '</span></span>
        </h4>
        </div>
        <div id="collapse' . $category . '" class="panel-collapse collapse">
        <ul class="list-group">
        <?php echo $appetizers_html; ?>';
        
        $menu_items_html .= $temp_str;
        
        $menu_items_html .= '</ul></div>';
        
        return $menu_items_html;
    }   
        ?>