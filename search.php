<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>乌云搜索|搜索结果</title>
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="bootstrap/js/jquery-3.1.0.min.js"></script>
	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="bootstrap/search.css">
</head>
<body>
<div id="my-container">
<div id="my-header">
	<strong class="my-words">search from wooyun.org</strong>
	<ul class="my-menu">
		<li class="my-item"><a class="lia" href="#">about</a></li>
		<li class="my-item"><a class="lia" href="index.html">home</a></li>
	</ul>
</div>
<div id="my-mainbody">
<?php
#接受参数
if(!isset($_GET['keywords'])){
	header("Location: index.html"); 
	exit;
}
else{
	$keywords=preg_replace("/[^a-zA-Z0-9\x{4e00}-\x{9fff}]+/u","",$_GET['keywords']);
	}
if(!isset($_GET['page'])){
 	$page=1;
}else{
	if(is_numeric($_GET['page'])&&is_int($_GET['page']+0)){
		$page=$_GET['page'];
	}
	else{
		$page=1;
	}
}
if(!isset($_GET['kind'])){
	$kind='bugs';
}
else{
	if($_GET['kind']==='bugs'||$_GET['kind']==='drops'){
	$kind=$_GET['kind'];
	}
	else{
	$kind='bugs';
	}
}
echo "<h3 style=\"display:inline-block;\">关键词【".$keywords."】的搜索结果：</h3>";
//mysql建立连接 
$db=new mysqli('localhost','root','','wooyun'); //localhost:3307
//sql对象错误检查 
if(mysqli_connect_errno()){ 
	echo '<br>Error:Please try again later.'; 
	exit(); 
}
// 建立查询 
$db->set_charset("utf8");
//分页处理
$query0="SELECT count(*) FROM `".$kind."` WHERE `title` LIKE '%".$keywords."%'";
$num=$db->query($query0);
$row=$num->fetch_row();
$rows=$row[0]/15;
if($page>$rows){
	$page=1;
}
echo "<h4 style=\"display:inline-block;\">共 ".$row[0]." 条记录</h4>";
$start=($page-1)*15;
$query="SELECT * FROM `".$kind."` WHERE `title` LIKE '%".$keywords."%' ORDER BY dates DESC LIMIT ".$start.",15";
//执行查询 
$result=$db->query($query); 
//逐行分解result 
echo "<table class=\"table table-striped table-hover\">";
echo "<tr><td>提交时间</td><td>标题</td><td>漏洞类型</td><td>提交者</td><tr>";
 for($i=0;$i<$result->num_rows;$i++){ 
 	$row_result=$result->fetch_object(); 
 	echo "<tr onclick=\"window.open('/wooyun/".$kind."/".$row_result->doc."');\" style=\"cursor:pointer;\">";
 	$time0=str_split($row_result->dates,10);
 	echo '<td>'.$time0[0].'</td>';
 	echo '<td style="color:#01a8ff;">'.$row_result->title.'</td>'; 
	echo '<td style="color:#01a8ff;">'.$row_result->type.'</td>';
 	echo '<td>'.$row_result->author.'</td>';
 	echo '</tr>'; 
 }
// 显示result 
// 释放连接,关闭sql. 
$result->free(); 
$db->close();
//分页
echo "<div class=\"pagination pagination-large my-page\">";
echo "<ul>";
if($row[0]%15==0){
	$total=$row[0]/15-1;
}
else{
	$total=($row[0]-$row[0]%15)/15;
}
$i=0;
if($page==1){
		echo "<li class=\"disabled\"><a href=\"#\">&laquo;</a></li>";
	}
	else{
		echo "<li><a href=\"search.php?kind=".$kind."&keywords=".$keywords."&page=".($page-1)."\">&laquo;</a></li>";
	}
if($total>20&&$page<=9){
	$total=17;
}
if($total>20&&$page>=9&&$page+8<=$total){
	$i=$page-9;
	$total=$page+8;
}
if($total>20&&$page>=9&&$page+8>$total){
	$i=$total-17;
}
for(;$i<=$total;$i++){ 
	if($page==$i+1){
		echo "<li class=\"disabled\"><a href=\"search.php?kind=".$kind."&keywords=".$keywords."&page=".($i+1)."\">".($i+1)."</a></li>";	
	}
	else{
		echo "<li><a href=\"search.php?kind=".$kind."&keywords=".$keywords."&page=".($i+1)."\">".($i+1)."</a></li>";	
	}
}
if($page==$total+1){
		echo "<li class=\"disabled\"><a href=\"#\">&raquo;</a></li>";
	}
	else{
		echo "<li><a href=\"search.php?kind=".$kind."&keywords=".$keywords."&page=".($page+1)."\">&raquo;</a></li>";
	}
echo "</ul></div>";
?>
<h5>数据来源于wooyun.org</h5>
</div>
</div>
</body>
</html>
