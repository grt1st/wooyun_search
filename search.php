<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>乌云搜索|搜索结果</title>
	<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="./bootstrap/js/jquery-3.1.0.min.js"></script>
	<script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="./bootstrap/search.css">
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
	$page=intval($_GET['page']);
}
if(!isset($_GET['kind'])){
	$kind='bugs';
}
else{
	if($_GET['kind']==='bugs'||$_GET['kind']==='drops'||$_GET['kind']==='author'){
		$kind=$_GET['kind'];
	}
	else{
		$kind='bugs';
	}
}

echo "<h3 style=\"display:inline-block;\">关键词【".$keywords."】的搜索结果：</h3>";
#$sql="select * from bugs where title like ";   
#$res=$pdo->query($sql);  
#SELECT count(*) FROM `bugs` WHERE `title` LIKE \'%腾讯%\' 
#SELECT* FROM `bugs` WHERE `title` LIKE '%腾讯%' LIMIT 0,4
#title,datas,author,doc,type 

//mysql建立连接 
$db=new mysqli('localhost','root','','wooyun'); 
//sql对象错误检查 
if(mysqli_connect_errno()){ 
	echo '<br>Error:Please try again later.'; 
	exit(); 
}
// 建立查询 
$db->set_charset("utf8");
#drops
//分页处理
if($kind==='author'){
	$query0="SELECT count(*) FROM `bugs` WHERE author LIKE '%".$keywords."%'";
	$query1="SELECT count(*) FROM `drops` WHERE author LIKE '%".$keywords."%'";
}
else{
	$query0="SELECT count(*) FROM `".$kind."` WHERE `title` LIKE '%".$keywords."%'";
}

$num=$db->query($query0);
$row=$num->fetch_row();
if($kind==="author"){
	$num1=$db->query($query1);
	$rows=$num1->fetch_row();
	$row[0]+=$rows[0];
}
//得到总页数 $p
if($row[0]%15!=0){
	$p=intval($row[0]/15)+1;	
}
else{
	$p=$row[0]/15;
}
if($row[0]==0) $p=1;
if($page>$p || $page<1){
	$page=1;
}
echo "<h4 style=\"display:inline-block;\">共 ".$row[0]." 条记录</h4>";
$start=($page-1)*15;
if($kind==='author'){
	$query="(SELECT doc,dates,title,type,author FROM `bugs` WHERE `author` LIKE '%".$keywords."%') UNION ALL (SELECT doc,dates,title,type,author FROM `drops` WHERE `author` LIKE '%".$keywords."%')ORDER BY dates DESC LIMIT ".$start.",15";
}
else{
	$query="SELECT * FROM `".$kind."` WHERE `title` LIKE '%".$keywords."%' ORDER BY dates DESC LIMIT ".$start.",15";
}
//执行查询 
$result=$db->query($query); 
//逐行分解result
#echo "<div>"; 
echo "<div><table class=\"table table-striped table-hover\">";
echo "<tr><td>提交时间</td><td>标题</td><td>漏洞类型</td><td>提交者</td><tr>";
 for($i=0;$i<$result->num_rows;$i++){ 
 	$row_result=$result->fetch_object(); 
 	if($kind!="author"){
 		echo "<tr onclick=\"window.open('./".$kind."/".$row_result->doc."');\" style=\"cursor:pointer;\">";
 	}
 	else{
 		if($row_result->type=="binary" || $row_result->type=="mobile" || $row_result->type=="mobiledev" || $row_result->type=="database" || $row_result->type=="news" || $row_result->type=="papers" || $row_result->type=="pentesting" || $row_result->type=="tips" || $row_result->type=="tools" || $row_result->type=="web" || $row_result->type=="wireless" || $row_result->type=="safe"){
 			echo "<tr onclick=\"window.open('./drops/".$row_result->doc."');\" style=\"cursor:pointer;\">";
 		}
 		else{
 			echo "<tr onclick=\"window.open('./bugs/".$row_result->doc."');\" style=\"cursor:pointer;\">";
 		}
 	} 	
 	$time0=str_split($row_result->dates,10);
 	echo '<td>'.$time0[0].'</td>';
 	echo '<td style="color:#01a8ff;">'.$row_result->title.'</td>'; 
	echo '<td style="color:#01a8ff;">'.$row_result->type.'</td>';
 	echo '<td>'.$row_result->author.'</td>';
 	echo '</tr>'; 
 }
echo "</table></div>";
// 显示result 
// 释放连接,关闭sql. 
$result->free(); 
$db->close();
//分页
echo "<div class=\"pagination pagination-large my-page\">";
echo "<ul>";
//首
if($page==1){
	echo "<li class=\"disabled\"><a href=\"#\">&laquo;</a></li>";
}
else{
	echo "<li><a href=\"search.php?kind=".$kind."&keywords=".$keywords."&page=".($page-1)."\">&laquo;</a></li>";
}
//中间处理
if($p>17){
	if($page<9){
		$i=1;
	}
	elseif($page>=9 && $page+8<$p){
		$i=$page-8;
	}
	elseif($page+8>=$p){
		$i=$p-8;
	}
	$j=$i+17;
}
else{
	$i=1;
	$j=$p;
}
//echo $p;
for(;$i<=$j;$i++){ 
	if($page==$i){
		echo "<li class=\"disabled\"><a href=\"search.php?kind=".$kind."&keywords=".$keywords."&page=".$i."\">".$i."</a></li>";	
	}
	else{
		echo "<li><a href=\"search.php?kind=".$kind."&keywords=".$keywords."&page=".$i."\">".$i."</a></li>";	
	}
}
//尾
if($page==$p || $p==0){
		echo "<li class=\"disabled\"><a href=\"#\">&raquo;</a></li>";
	}
	else{
		echo "<li><a href=\"search.php?kind=".$kind."&keywords=".$keywords."&page=".($page+1)."\">&raquo;</a></li>";
	}
echo "</ul></div>";
?>
<div><h5>数据来源于wooyun.org</h5></div>
</div>
</div>
</body>
</html>
