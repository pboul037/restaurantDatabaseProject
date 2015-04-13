<?php
session_start();



    // include configuration
    require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');

    $dal =  new DAL();
    $raters_list= get_raters_list();
    $total_rater_ratings;
    $rater_reputation;





    function get_raters_list(){
    	global $dal, $raters_list;
    	$is_New_Sorting_Selected 	= 	isset($_POST['all_raters_sorting']);
    	$is_Old_Sorting_Selected 	= 	isset($_SESSION['all_raters_sorting']);
    	$is_clear_all_search_options 	= 	isset($_POST['clear_all_search_options']);
       	$sorting = null;

    	if($is_clear_all_search_options){
       		unset($_SESSION['all_raters_sorting']);
       	}
        else if($is_New_Sorting_Selected){
        	$_SESSION['all_raters_sorting'] = $_POST['all_raters_sorting'];
        	$sorting = $_POST['all_raters_sorting'];
        }
        else if($is_Old_Sorting_Selected){
        	$sorting = $_SESSION['all_raters_sorting'];
        }

        $raters_list = $dal->get_all_raters($sorting);
        $raters_list_html = get_raters_html_items($raters_list);

        if($is_New_Sorting_Selected ){
           // send the response
        	//echo "hi";
           echo json_encode($raters_list_html);
        } 
        return $raters_list;
    }

    function get_raters_html_items($raters_list) {
    	global $dal;

    	$raters_html_items = '';

		foreach($raters_list as $rater){
			$tmp = $dal->getRaterRatings($rater->rater_id, null);
			$last_rated = "No Ratings";
			if(isset($tmp[0]))
				$last_rated = $tmp[0]->date_written;
            $raters_html_items .= 
            '<div class="list-group-item">
                <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-12"
                        <span class="col-sm-12">
                                <a style="font-size:16pt" href="ARater.php?rater=' . $rater->_name .'">' 
                        			. $rater->_name .' </a>
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <span class="col-sm-5"> Rater ID:</span>
                            <span class="col-sm-1">' . $rater->rater_id .'</span>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-sm-4">
	                    	<span class="col-sm-1 glyphicon glyphicon-star" style="color:green"></span>
	                        <span class="col-sm-1">' . $rater->rater_reputation .'</span>
	                        <span class="col-sm-5"> Reputation</span>
                    	</div>
                    	<div class="col-sm-4">
	                       <span class="col-sm-2"> Type: </span>
	                       <span class="col-sm-5">' . $rater->_type .'</span>
                    	</div>
                    	<div class="col-sm-4">
	                       <span class="col-sm-7"> Join Date: </span>
	                       <span class="col-sm-2">' . $rater->join_date .'</span>
                    	</div>
                    </div>
                    <div class="row">
                    	<div class="col-sm-4">
                        	<span class="col-sm-1 glyphicon glyphicon-stats" style="color:green"></span>
                        	<span class="col-sm-1">' . $rater->total_num_ratings .'</span>
                        	<span class="col-sm-7"> Total Ratings</span>
                        </div>
                        <div class="col-sm-4">
	                       <span class="col-sm-2"> Email: </span>
	                       <span class="col-sm-2">' . $rater->email .'</span>
                    	</div>
                    	<div class="col-sm-4">
	                       <span class="col-sm-7"> Last Rating Date: </span>
	                       <span class="col-sm-5">' . $last_rated .'</span>
                    	</div>
                    </div>
                    </div>
                    <div style="vertical-align:middle" class="col-sm-1">

                          
                    </div>
                </div>
            </div>';
    }
    return $raters_html_items;
    }

?>