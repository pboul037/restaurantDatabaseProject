<html>
    <head>
        <meta http-equiv="Content Type" content="text/html; charset_UTF-8">
        <link rel="stylesheet" type="text/css" href="css/style1.css" />
        <title>Student Database</title>
        <?php
        if(array_key_exists('save', $_POST)){
            $studentnum=$_POST['istudentnum'];
            $lastname=$_POST['ilastname'];
            $firstname=$_POST['ifirstname'];
            $password=$_POST['ipassword'];
            $street=$_POST['istreet'];
            $city=$_POST['icity'];
            $gender=$_POST['igender'];
            $email=$_POST['iemail'];
            $conn_string="host=localhost port=5432 dbname=postgres user=postgres password=G0d$1Lla";
            echo "Student Number : " . $studentnum ."\n" .
                "Last Name : " . $lastname ."\n" .
                "First Name : " . $lastname ."\n" .
                "Password : " . $password ."\n" .
                "Street : " . $street ."\n" .
                "City : " . $city ."\n" .
                "Gender : " . $gender ."\n" .
                "Email : " . $email;
            $dbconn=pg_connect($conn_string) or die ('Connection Failed');
            $query="INSERT INTO php_project.student( student_num, last_name, first_name, student_pass, street, city, gender, email) VALUES 
                        ( '$studentnum', '$lastname', '$firstname', '$password', '$street', '$city', '$gender', '$email')";
            $result=pg_query($dbconn, $query);
            if(!$result){
                die("Error in SQL query:" .pg_last_error());
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
        <div id="header">USER REGISTRATION FORM</div>
        <form id="testForm" name="testForm" method="post" action="" >
            <p> <label for="istudentnum">Student #:</label>
                <input name="istudentnum" type="text" id="repno" />
            </p>
            <p> <label for="ilastname">Last Name:</label>
                <input name="ilastname" type="text" id="ilastname" />
            </p>
            <p> <label for="ifirstname">First Name:</label>
                <input name="ifirstname" type="text" id="ifirstname" />
            </p>
            <p> <label for="ipassword">Password:</label>
                <input name="ipassword" type="password" id="ipassword" />
            </p>
            <p> <label for="iconfpass">Confirm password:</label>
                <input name="iconfpass" type="password" id="iconfpass" />
            </p>
            <p>
                <label for="istreet">Street:</label>
                <input name="istreet" type="text" id="istreet" />
            </p>
            <p>
                <label for="icity" id="formLabel">City</label>
                <input name="icity" type="text" id="icity" />
            </p>
            <p>
                <label for="igender">Gender:</label>
                <select name="igender">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </p>
            <p>
                <label for="iemail">Email:</label>
                <input name="iemail" id="iemail" type="text" />
            </p>
            <p>
                <input type="submit" name="save" value="Register"/>
            </p>
        </form>
    </body>
</html>