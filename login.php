<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<title>Student Database</title>
<?php
    session_start();
    // Check if thelogin button was clicked and the login value is in the POST array object
    if(array_key_exists('login', $_POST))
    {
        // Retreive the user login and password from the login form
        $studentnum=$_POST['studentnum'];
        $password=$_POST['userPassword'];
        
        // Get database connection started
        $conn_string="host=localhost port=5432 dbname=postgres user=postgres password=G0d$1Lla";
        
        // Connect to database
        $dbconn=pg_connect($conn_string) or die('Connection failed');
        
        // Query database to see if user exist
        // Use parameters to avoid  sql injection
        $query="SELECT * FROM php_project.Student WHERE Student_NUM=$1 AND STUDENT_PASS=$2";
        
        // Prepare the statement to avoid sql injection
        $stmt=pg_prepare($dbconn, "ps", $query);
        $result=pg_execute($dbconn, "ps", array($studentnum, $password));
        
        if(!$result){
            die("Error in SQL query:" .pg_last_error());
        }
        
        // Check row count if row count is greater than 0 record exist
        $row_count=  pg_num_rows($result);
        if($row_count> 0){
            
            // Keep user information accross pages and redirect to record page
            $_SESSION['studentnum']=$studentnum;
            header("location: http://localhost/restaurantRatingDbProject/records.php");
            exit;
        }
        
        echo "Data Successfully Entered ". "<a href='index.php'>login now</a>";
        
        // free memory
        pg_free_result($result);
        // close connection
        pg_close($dbconn);
    }
    ?>
</head>
	
<body>
    <div class="container">
        <div class="row">
                <img class="center-block smeets-logo"/>
                <h3 style="text-align:center">Restaurant Ratings</h3>
            <div class="col-sm-6 col-md-4 col-md-offset-4">

                <div class="alert alert-danger" role="alert" data-bind="visible: errorMessage, text: errorMessage"></div>

                <div class="account-wall">
                    <form class="form-signin">
                        <input type="text" data-bind="value: username" class="form-control" placeholder="Username" required autofocus />
                        <input type="password" data-bind="value: password" class="form-control" placeholder="Password" required />

                        <button class="btn btn-lg btn-primary btn-block" type="submit" data-bind="click: login">Log me in!</button>
                        <button class="btn btn-lg btn-success btn-block" type="submit" data-bind="click: signup"> Sign me up!</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
	<div id="header"> USER LOGIN FORM</div>
	<form method="POST" action="">
		<p>Student #: <input type="text" name="studentnum" id="studentnum"/></p>
		<p>Password: <input type="password" name="userPassword" id="userpassword" /></p>
		<p><input type="submit" value="login" name="login" /></p>
	</form>
	<a href="register.php">Register</a>
</body>
</html>
