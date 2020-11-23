<?php         
//https://github.com/hcymysql/mysql_monitor

     $con = mysqli_connect("localhost","root","Sgl20@14","sql_db","3306") or die("数据库链接错误".mysqli_error($con));
     mysqli_query($con,"set names utf8");  
?> 
