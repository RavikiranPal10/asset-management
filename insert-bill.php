<?php

	$f1 = $_POST['f1'];
	$f2 = $_POST['f2'];
	$f3 = $_POST['f3'];
	$f4 = $_POST['f4'];
	$f5 = $_POST['f5'];
	$f6 = $_POST['f6'];
	$f7 = $_POST['f7'];
	$f8 = $_POST['f8'];

	
	
		$con = mysql_connect("127.0.0.1:3306","root","");
		
		if(! $con)
		{
			die('Connection Failed'.mysql_error());
		}
		
		
		mysql_select_db("purchase",$con) or die("no db :'(");
		$sql="insert into bill(f1,f2,f3,f4,f5,f6,f7,f8)"."values('$f1','$f2','$f3','$f4','$f5','$f6','$f7','$f8')";
		$result = mysql_query($sql, $con) 

 or die("Execution Of The SQL Query Failed insert");
		 header("location: welcome.php");
?>