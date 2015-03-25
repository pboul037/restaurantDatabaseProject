<?php
        session_start();

        //Retrieve login id from session object
        //if(!isset($_SESSION['loginid'])){
        //    echo "Please" . "<a href='Login.php'>Login</a>";
        //    exit;
        //}
        
        // include configuration
        require_once(dirname(dirname(__FILE__)) . '\etc\conf\config.php');
            
        $dal =  new DAL();

        $details = $dal->get_location_details($_GET['locationid']);
        $details = $details[0];

        $location_ratings_list = $dal->get_location_ratings($_GET['locationid']);

        $address_for_gmaps_splitted = explode( ",", $details->street_address );
        $address_for_gmaps = join('+', $address_for_gmaps_splitted);

        $opening_hours = explode(",", $details->opening_hours);
        
        $beverage_categories = $dal->get_beverages_categories($details->location_id);
        $beverages_menu_items_by_category = array();

        //get food menu items html
        $appetizers_html = get_menu_items('food', 'appetizer');
        $mains_html = get_menu_items('food', 'main');
        $desserts_html = get_menu_items('food', 'dessert');
        
        foreach($beverage_categories as $category){
            $beverages_menu_items_by_category[$category->category] = get_menu_items('drink', $category->category);
        }
        

        /*
         * Returns a $location_rating list html item as 
         * a string.
         *
         * @author Junyi Dai
         */
        function get_location_rating_html_items($location_ratings_list){
           $location_rating_html_item = "";
            foreach($location_ratings_list as $location_rating){
                $location_rating_html_item .= 
                '<div class="list-group-item"><div class="row">
                    <div class="col-sm-4">
                        <div class="row">
                            <span class="col-sm-12">
                                <span style="font-size:16pt; font-weight:bold;">' . $location_rating->avg_rating . '</span>
                                <span style="font-size:10pt">out of 5 </span>
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