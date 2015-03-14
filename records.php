<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
		<title>Student Information</title>
        
        <?php
        
        session_start();

        //Retrieve student number from session object
        if(!isset($_SESSION['studentnum'])){
            echo "Please" . "<a href='Login.php'>Login</a>";
            exit;
        }
        
        $dbh=pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=G0d$1Lla");
        if (!$dbh) {
            die("Error in connection: " . pg_last_error());
        }
        
        $studentnum = $_SESSION['studentnum'];

        // Query to select from three tables
        $sql = "SELECT c.COURSE, g.YEAR, g.SEC,
                g.GRADE FROM php_project.Student s, php_project.Grades g, php_project.Courses c
                WHERE s.Student_Num =$1";
        $stmt = pg_prepare($dbh, "ps", $sql);

        $result = pg_execute($dbh, "ps", array($studentnum));
        if (!$result) {
            die("Error in SQL query: " . pg_last_error());
        }
        
        //free memory
        //pg_free_result($result);
        //close connection
        pg_close($dbh);
        ?>
        
	</head>
	<body>
		<div id="header"><h1>Student Record Details</h1></div>
		<table>
			<tr>
				<th>Course</th>
				<th>Year</th>
				<th>Session</th>
				<th>Grade</th>
			</tr>
            <?php while($row=pg_fetch_array($result)) { ?>
			<tr>
				<td><?php echo $row[0]; ?><td>
				<td><?php echo $row[1]; ?></td>
				<td><?php echo $row[2]; ?></td>
				<td><?php echo $row[3]; ?></td>
			</tr>
            <?php } ?>
		</table>
	</body>
	
</html>	