<?php

    //date_default_timezone_set(PRC);
    ini_set('date.timezone','Asia/Shanghai');
    /*
    session_start();

    //检测是否登录，若没登录则转向登录界面
    if(!isset($_SESSION['userid'])){
        header("Location:../index.html");
        exit("你还没登录呢。");
    }*/
    
?>

<!doctype html>
<html class="x-admin-sm">
<head>
    <meta http-equiv="Content-Type"  content="text/html;  charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="refresh" content="600" />  <!-- 页面刷新时间600秒 -->
    <title>MySQL 状态监控</title>

<style type="text/css">
a:link { text-decoration: none;color: #3366FF}
a:active { text-decoration:blink;color: green}
a:hover { text-decoration:underline;color: #6600FF}
a:visited { text-decoration: none;color: green}
</style>

    <script type="text/javascript" src="xadmin/js/jquery-3.3.1.min.js"></script>
    <script src="xadmin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="xadmin/js/xadmin.js"></script>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/simple-line-icons.css">
    <link rel="stylesheet" href="./css/fontawesome-all.min.css">
    <link rel="stylesheet" href="./css/styles.css">

<script language="javascript">
function TestBlack(TagName){
 var obj = document.getElementById(TagName);
 if(obj.style.display=="block"){
  obj.style.display = "none";
 }else{
  obj.style.display = "block";
 }
}
</script>

<!--
<script>
function ss(){
var slt=document.getElementById("select");
if(slt.value==""){
        alert("请选择数据库!!!");
        return false;
}
return true;
}
</script>
-->
</head>

<body>
<div class="card">
<div class="card-header bg-light">
    <h1><a href="mysql_status_monitor.php">MySQL 状态监控</a></h1>
</div>
      
<div class="card-body">
<div class="table-responsive">
                
<form action="" method="post" name="sql_statement" id="form1" onsubmit=" return ss()">
  <div>
    <tr>
        <td><p align='left'>输入IP地址:
 	   <input type='text' name='dbip' value=''>	

           <select id="select" name="dbname">
	     <option value="">选择你的数据库</option>
	<?php
	
	require 'conn.php';
	$result = mysqli_query($con,"SELECT dbname FROM mysql_status_info group by dbname");
	while($row = mysqli_fetch_array($result)){
		//保留下拉列表框选项
                    if(isset($_POST['dbname']) || isset($_GET['dbname'])){
                        if($_POST['dbname'] == $row[0] || $_GET['dbname'] == $row[0]){
                            echo "<option selected='selected' value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
                        } else {
                            echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
                        }
                    } else{ echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";}

		//echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
    }
	
    ?>
        </select>

	<select id="select" name="dbrole">
	    <option value="">选择角色</option>
	<?php
	    require 'conn.php';
            $result = mysqli_query($con,"SELECT DISTINCT(role) FROM mysql_status");	
            while($row = mysqli_fetch_array($result)){
		//$dbrole_original=$row[0];
		//$dbrole=$row[0]==1?'主':'从';
                //保留下拉列表框选项
                    if(isset($_POST['dbrole']) || isset($_GET['dbrole'])){
			//$dbrole=$row[0]==1?'是':'否';
                        if($_POST['dbrole'] == $row[0] || $_GET['dbrole'] == $row[0]){
			    //$dbrole=$row[0]==1?'是':'否';
                            echo "<option selected='selected' value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
                        } else { 
                            echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
                        }
                    } else{ echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";}
                
                //echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
            }
	?>
        </select>
	

            &nbsp;&nbsp;输入MySQL端口号:
           <input type='text' name='dbport' value=''>
<td>
    </tr>
    <input name="submit" type="submit" class="STYLE3" value="搜索" />
    </label>
  </div>
</form>


<?php
echo "<table border='0' width='100%'>";
echo "<tr>";
echo "<td>监控采集阀值是每1分钟/次</td>";
echo "<td><p align='right'>最新监控时间:".date('Y-m-d H:i:s')."</td>";
echo "</tr>";
echo "</table>";
	
    if(isset($_POST['submit'])){
        $dbname=$_POST['dbname'];
        $dbip=$_POST['dbip'];
        $dbport=$_POST['dbport'];
	$dbrole=$_POST['dbrole'];
        //session_start();
	//$_SESSION['transmit_dbname']=$dbname;
        //require 'show.html';
    } else {
	//require 'top.html';
    }

?>

<table style='width:100%;font-size:14px;' class='table table-hover table-condensed'>                                    
<thead>                                   
<tr>                                                                         
<th>主机</th>
<th>数据库名</th>
<th>端口</th>
<th>角色</th>
<th>状态</th>
<th>最大连接数</th>
<th>活动连接数</th>
<th>每秒查询</th>
<th>每秒插入</th>
<th>每秒更新</th>
<th>每秒删除</th>
<th>运行时间</th>
<th>版本</th>
<th>图表</th>
</tr>
</thead>
<tbody>

<?php
    require 'conn.php';

$perNumber=500; //每页显示的记录数  
$page=$_GET['page']; //获得当前的页面值  
$count=mysqli_query($con,"select count(*) from mysql_status"); //获得记录总数
$rs=mysqli_fetch_array($count);   
$totalNumber=$rs[0];  
$totalPage=ceil($totalNumber/$perNumber); //计算出总页数  

if (empty($page)) {  
 $page=1;  
} //如果没有值,则赋值1

$startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录 

    $condition.="1=1 ";	
    if(!empty($dbname)){
    	$condition.=" AND dbname='{$dbname}'";
    }
    if(!empty($dbip)){
    	$condition.=" AND host='{$dbip}'";
    }
    if(!empty($dbport)){
    	$condition.=" AND port='{$dbport}'";
    }
    if(!empty($dbrole)){
        $condition.=" AND role={$dbrole}";
    }
   
	$sql = "SELECT * FROM mysql_status WHERE $condition order by dbname ASC,id ASC LIMIT $startCount,$perNumber";
 	//echo $sql."<br>";   

$result = mysqli_query($con,$sql);

//echo "复制监控采集阀值是每1分钟/次    最新监控时间：".date('Y-m-d H:i:s')."</br>";

while($row = mysqli_fetch_array($result)) 
{
    if($row['5']==0){
	$role='<span class="badge badge-secondary">未知</span>';
    } else {
	$role=$row['4']==0?'<span class="badge badge-warning">slave</span>':'<b><span class="badge badge-primary">master</span></b>';
    }
$status=$row['5']==1?'<b><span class="badge badge-success">在线</span></b>':'<span class="badge badge-danger">宕机</span>';
echo "<tr>";
echo "<td>{$row['1']}</td>";
//echo "<td>{$row['2']}</td>";
echo "<td><a href='javascript:void(0);' onclick=\"x_admin_show('统计库里每个表的大小','table_statistic.php?ip={$row['1']}&dbname={$row['2']}&port={$row['3']}')\">{$row['2']}</a></td>";
echo "<td>{$row['3']}</td>";
echo "<td>{$role}</td>";
echo "<td>{$status}</td>";
echo "<td>{$row['6']}</td>";
echo "<td><a href='javascript:void(0);' onclick=\"x_admin_show('连接数详情','db_connect_statistic.php?ip={$row['1']}&dbname={$row['2']}&port={$row['3']}')\">{$row['7']}</a></td>";
//echo "<td>{$row['7']}</td>";
echo "<td>{$row['8']}</td>";
echo "<td>{$row['9']}</td>";
echo "<td>{$row['10']}</td>";
echo "<td>{$row['11']}</td>";
echo "<td>{$row['14']} 天</td>";
echo "<td>{$row['15']}</td>";
echo "<td><a href='javascript:void(0);' onclick=\"x_admin_show('历史信息图表','show_graph.php?ip={$row['1']}&dbname={$row['2']}&port={$row['3']}')\"><img src='image/chart.gif' /></a></td>";
echo "</tr>";
}
//end while
echo "</tbody>";
echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

$maxPageCount=10; 
$buffCount=2;
$startPage=1;
 
if  ($page< $buffCount){
    $startPage=1;
}else if($page>=$buffCount  and $page<$totalPage-$maxPageCount  ){
    $startPage=$page-$buffCount+1;
}else{
    $startPage=$totalPage-$maxPageCount+1;
}
 
$endPage=$startPage+$maxPageCount-1;
 
 
$htmlstr="";
 
$htmlstr.="<table class='bordered' border='1' align='center'><tr>";
    if ($page > 1){
        $htmlstr.="<td> <a href='mysql_status_monitor.php?page=" . "1" . "'>第一页</a></td>";
        $htmlstr.="<td> <a href='mysql_status_monitor.php?page=" . ($page-1) . "'>上一页</a></td>";
    }

    $htmlstr.="<td> 总共${totalPage}页</td>";

    for ($i=$startPage;$i<=$endPage; $i++){
         
        $htmlstr.="<td><a href='mysql_status_monitor.php?page=" . $i . "'>" . $i . "</a></td>";
    }
     
    if ($page<$totalPage){
        $htmlstr.="<td><a href='mysql_status_monitor.php?page=" . ($page+1) . "'>下一页</a></td>";
        $htmlstr.="<td><a href='mysql_status_monitor.php?page=" . $totalPage . "'>最后页</a></td>";
 
    }
$htmlstr.="</tr></table>";
echo $htmlstr;

?>
