<?php

	$f1 = $_POST['f1'];
	$f2 = $_POST['f2'];
	
	
	
	
		$con = mysql_connect("127.0.0.1:3306","root","");
		
		if(! $con)
		{
			die('Connection Failed'.mysql_error());
		}
		
		
		mysql_select_db("purchase",$con) or die("no db :'(");
		$sql="insert into login(f1,f2)"."values('$f1','$f2')";
		$result = mysql_query($sql, $con) 

 or die("Execution Of The SQL Query Failed insert");
		 header("location: welcome.php");
?>