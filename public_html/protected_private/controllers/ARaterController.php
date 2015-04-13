<?php
        /*
         * Controller for the restaurants page.
         *
         * @author Qasim Ahmed
         */

        session_start();
        
        // include configuration
        require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');
        $dal =  new DAL();                          // data access layer
        

        //Gets the db information on the current rater including his user infomation
        $rater = $dal->getRaterInformation(getRaterName())[0];

        //Gets all the ratings made by this rater
        $rater_ratings;
        //Gets the date of the last rating
        $last_rated = dateOfLastRating();
        getAllRatingsForRater();


        

        /*
        Gets the last the date of the last rated rating
        Returns the string "No Ratings" if there was no ratings
        @author Qasim Ahmed
        */
        function dateOfLastRating(){
        	global $dal, $rater;
        	$last_rated = "No Ratings";
        	$tmp = $dal->getRaterRatings($rater->rater_id, null);
        	if(isset($tmp[0]))
        		$last_rated = $tmp[0]->date_written;
        	return $last_rated;

        }

		/*
        * Generates html for a delete account botton 
        * @author Qasim Ahmed
        */
        function deleteButton(){
        	global $rater;
        	$button = "";
        	$rater_id= $rater->rater_id;

        	if(isset($_SESSION['username'])){
        			if($_SESSION['username'] == 'admin' || $_SESSION['username'] == $rater->_name){
        			//At this point by elimination the user or admin is signed in
        			$button = ' <button id="delete_account_bt" type="button" class="btn btn-default" 
                                onclick="deleteAccount(' . $rater_id . ')" 
                                    > Delete Account</button>' ;
                    }
            }
            // //debugging
            // $button = ' <button id="delete_account_bt" type="button" class="btn btn-default" 
            //                     onclick="deleteAccount()" 
            //                         > Delete Account</button>';

            return $button;            
        }




        /*
        * Gets a list of ratings made by this rater
        * @author Qasim Ahmed
        */
        function getAllRatingsForRater(){
        	global $rater_ratings, $dal, $rater;
        	$is_New_Sorting_Selected = isset($_POST['raters_sorting']);
         	$is_Old_Sorting_Selected = isset($_SESSION['raters_sorting']);
         	$is_clear_all_search_options = isset($_POST['clear_all_search_options']);
         	$rater_id = $rater->rater_id;
         	$sorting = null;
       		
       		if($is_clear_all_search_options){
       			$sorting = null;
       			unset($_SESSION['raters_sorting']);
       		}
        	else if($is_New_Sorting_Selected){
        		$_SESSION['raters_sorting'] = $_POST['raters_sorting'];
        		$sorting = $_POST['raters_sorting'];
        	}
        	else if($is_Old_Sorting_Selected){
        		$sorting = $_SESSION['raters_sorting'];
        	}


        	$rater_ratings = $dal->getRaterRatings($rater_id, $sorting);
        	$raters_rating_html = getAllRatingsForRaterHTML($rater_ratings);

        	if($is_New_Sorting_Selected ){
            // send the response
        		//echo "hi";
            echo json_encode($raters_rating_html);
        	} 

        	return $rater_ratings;
        }

        /*
        * Returns the list of ratings made by this rater in html ready to be sent to the view
        * author Qasim Ahmed
        */
        function getAllRatingsForRaterHTML($ratings_list){
        	 global $dal, $rater_ratings, $rater;
        //QA
        //Declaring the variable that will hold all the HTML code that will be returned to the view
        $ratings_list_html = '';

        //JD: Declaring the variable that will hold the dollar sign string to store the price rating (out of 5)
        $dollar_sign_string = '';

       //QA
       //Loops through the the list of ratings and generates html for each one to display on the view
        foreach($ratings_list as $rating){

            // add gold $ for actual price avg and store it in $dollar_sign_string
            for( $i = 0; $i < $rating->price; $i++){
                $dollar_sign_string .= '<span style="font-size:12px" class="glyphicon glyphicon-usd" style="color:black"></span>';
            }

            // add the subtraction of 5 by avg price of grey $ and store it in $dollar_sign_string
            for( $i = 0; $i < 5-$rating->price; $i++){
                $dollar_sign_string .= '<span  style="font-size:12px" class="glyphicon glyphicon-usd" style="color:#DCDCDC"></span>';
            }

            $ratings_list_html .= 
            '<div class="list-group-item">
                <div class="row">
                <div class="col-sm-3">
                    <div class="row"
                            <span style="text-align:center" class="col-sm-5">
                            	<h4 style="font-weight:bold">' . $rating->_name . '</h4>
                                <h4 style="font-weight:bold">' . $rating->avg_rating . '<small style="padding-right:25px"> out of 5</small>' . $dollar_sign_string . '</h4>
                            </span>
                    </div>
                    <div class="row">
                            <span style="text-align:center" class="col-sm-4">
                                <span style="font-size:10pt;">Food </span>
                                <h5 style="font-weight:bold">' . $rating->food . '<small>/5</small></h5>
                            </span>
                            <span style="text-align:center" class="col-sm-4">
                                <span style="font-size:10pt;">Ambiance </span>
                                <h5 style="font-weight:bold">' . $rating->ambiance . '<small>/5</small></h5>
                            </span>
                            <span style="text-align:center" class="col-sm-4">
                                <span style="font-size:10pt;">Service </span>
                                <h5 style="font-weight:bold">' . $rating->service . '<small>/5</small></h5>
                            </span>
                    </div>


                    <span style="font-size:12pt">On: </span>
                    <span style="font-size:10pt;">' . $rating->date_written . '</span>
                    <div class="row">
                        <span class="col-sm-1 glyphicon glyphicon-star" style="color:green"></span>
                        <span class="col-sm-1">' . $rater->rater_reputation .'</span>
                        <span class="col-sm-5">Reputation</span>
                    </div>
                    <div class="row">
                        <span class="col-sm-1 glyphicon glyphicon-stats" style="color:green"></span>
                        <span class="col-sm-1">' . $rater->total_num_ratings.'</span>
                        <span class="col-sm-5">total ratings</span>
                    </div>
                                       <div class="row">
                        <span class="col-sm-12">Rated this location ' . $dal->numRatingsFromRaterForLocation($rating->location_id, $rater->rater_id)[0]->rater_ratings_for_this_loc.' time(s)</span>
                    </div>
                    </div>
                    <div class="well col-sm-8"><span class="col-sm-2">Ordered: </span>';
                    $rating_items_for_rating = $dal->get_rating_items_for_rating($rating->rating_id);
                    foreach($rating_items_for_rating as $rating_item){
                        $ratings_list_html .= '<span class="tagcloud tag label label-warning">' . $rating_item->_name . ' :                                                                 $' . $rating_item->price . '</span>';
                    }
                    if( count($rating_items_for_rating) < 1)
                        $ratings_list_html .= '<span>No order specified. </span>';
                        
        $ratings_list_html .= '</div>
                    <div class="col-sm-8 comment">
                        <div class="row">
                            <div class="col-sm-12
                                    <span style="font-size:10pt;">' . 
                                        (strlen($rating->_comments) > 0 ? $rating->_comments : "No comments") 
                                    . '</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';

            // Clear the dollar sign string
            $dollar_sign_string = '';
    }
    return $ratings_list_html;
        }

		//QA
		//Gets the raters name from the url
		function getRaterName(){

		//Whenever we update we cannot use the url to get the rater name so we send that info to the post and read it
		if(isset($_POST['ratername'])){
			$result = $_POST['ratername'];
		}
		//On the first request for the page we read the rater name from the url
		else{
		//Gets the full url
		$str ="http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		//splits the url string on "?"
		$temp = explode( "?", $str );
		$temp2 = explode( "=", $temp['1'] );
		$result = $temp2[1];
		}
		if($result == "Deleted%20User")
			$result = "Deleted User";
		

		//Returns what should be the user name
		return $result;
		}

?>