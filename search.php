<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<title>乌云搜索|搜索结果</title>
	<link href="//lib.baomitu.com/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="//lib.baomitu.com/jquery/3.1.0/jquery.min.js"></script>
	<script type="text/javascript" src="//lib.baomitu.com/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style type="text/css">
		.container{
            width: auto;
			max-width: 1500px;
			margin: 0 auto;
			position: relative;
			top: 50px;
            padding-bottom: 50px;
		}
	</style>
</head>
<body>

	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="navbar-left">
			<a class="navbar-brand" href="#">search from wooyun.org</a>
		</div>
		<div class="navbar-right">
			<ul class="nav navbar-nav">
				<li><a href="./index.html">home</a></li>
				<li><a href="#">about</a></li>
				<li><a href=""></a></li>
			</ul>
		</div>
	</nav>

<div class="container">

<?php

require './config.php';

#接受参数
if (!isset($_GET['keywords']))
{
	header("Location: index.html"); 
	exit;
}
else
{
	$keywords = preg_replace("/[^a-zA-Z0-9\x{4e00}-\x{9fff}]+/u", "", $_GET['keywords']);
}

if (!isset($_GET['page']))
{
 	$page = 1;
}
else
{
	$page = intval($_GET['page']);
}

if (!isset($_GET['kind']))
{
	$kind = 'bugs';
}
else
{
	if ($_GET['kind'] === 'bugs' || $_GET['kind'] === 'drops' || $_GET['kind'] === 'author')
	{
		$kind = $_GET['kind'];
	}
	else
	{
		$kind = 'bugs';
	}
}

echo "<h3 style=\"display:inline-block;\">关键词<strong>【{$keywords}】</strong>的搜索结果：</h3>";

//pdo连接mysql
try
{
    $conn = new PDO("mysql:host={$config['host']};dbname={$config['database']};port={$config['port']};charset=utf8", $config['user'], $config['passwd']);
    //echo "mysql:host={$config['host']};dbname={$config['database']}";
}
catch (PDOException $pe)
{
    die("Could not connect to the database $dbname :" . $pe->getMessage());
}

//查询数目
if ($kind === 'author')
{
    $query0 = "SELECT count(*) FROM `bugs` WHERE author LIKE '%{$keywords}%'";
    $query1 = "SELECT count(*) FROM `drops` WHERE author LIKE '%{$keywords}%'";
    $res0 = $conn->query($query0);
    $res1 = $conn->query($query1);
    $row = $res0->fetch(PDO::FETCH_NUM)[0] + $res1->fetch(PDO::FETCH_NUM)[0];
}
else
{
    $query0="SELECT count(*) FROM `{$kind}` WHERE `title` LIKE '%{$keywords}%'";
    $res0 = $conn->query($query0);
    $row = $res0->fetch(PDO::FETCH_NUM)[0];
}

//得到总页数 $p
if ($row % 15 != 0)
{
    $p = intval($row / 15) + 1;
}
else
{
    $p = $row / 15;
}
if ($row == 0) $p = 1;
if ($page > $p || $page <1 ) $page = 1;

echo "<h4 style=\"display:inline-block;\">共 {$row} 条记录</h4>";

$start = ($page - 1) * 15;

if ($kind === 'author')
{
    $query = "(SELECT doc,dates,title,type,author FROM `bugs` WHERE `author` LIKE '%{$keywords}%') UNION ALL (SELECT doc,dates,title,type,author FROM `drops` WHERE `author` LIKE '%{$keywords}%')ORDER BY dates DESC LIMIT {$start},15";
}
else
{
    $query = "SELECT * FROM `{$kind}` WHERE `title` LIKE '%{$keywords}%' ORDER BY dates DESC LIMIT {$start},15";
}

$res = $conn->query($query);
$resulte = $res->fetchAll(PDO::FETCH_ASSOC);
//var_dump($resulte);

//输出结果
echo '<div><table class="table table-striped table-hover">';
echo '<tr><td>提交时间</td><td>标题</td><td>漏洞类型</td><td>提交者</td><tr>';

foreach ($resulte as $rrr){
    if ($kind != "author")
    {
        echo "<tr onclick=\"window.open('./{$kind}/{$rrr['doc']}');\" style=\"cursor:pointer;\">";
    }
    else
    {
        if (array_key_exists($rrr['type'], $drops))
        {
            echo "<tr onclick=\"window.open('./drops/{$rrr['doc']}');\" style=\"cursor:pointer;\">";
        }
        else
        {
            echo "<tr onclick=\"window.open('./bugs/{$rrr['doc']}');\" style=\"cursor:pointer;\">";
        }
    }

    $time0 = str_split($rrr['dates'], 10);
    echo "<td>{$time0[0]}</td>";
    echo "<td style=\"color:#01a8ff;\">{$rrr['title']}</td>";
	if (array_key_exists($rrr['type'], $drops))
	{
		echo "<td style=\"color:#01a8ff;\">{$drops[$rrr['type']]}</td>";
	}
    else
	{
		echo "<td style=\"color:#01a8ff;\">{$rrr['type']}</td>";
	}
    echo "<td>{$rrr['author']}</td>";
    echo "</tr>";
}
echo "</table></div>";

$conn = null;

//分页
echo '<ul class="pagination pagination-lg">';
//首
if ($page==1)
{
	echo '<li class="disabled"><a href="#">&laquo;</a></li>';
}
else
{
	echo "<li><a href=\"search.php?kind={$kind}&keywords={$keywords}&page={($page-1)}\">&laquo;</a></li>";
}
//中间处理
if ($p > 17)
{
	if ($page < 9)
	{
		$i=1;
	}
	elseif ($page >= 9 && $page + 8 < $p)
    {
		$i = $page-8;
	}
	elseif ($page + 8 >= $p)
    {
		$i = $p-8;
	}
	$j = $i + 17;
}
else
{
	$i = 1;
	$j = $p;
}
//echo $p;
for (;$i <= $j;$i++)
{
	if ($page == $i)
	{
		echo "<li class=\"disabled\"><a href=\"search.php?kind={$kind}&keywords={$keywords}&page={$i}\">{$i}</a></li>";
	}
	else
    {
		echo "<li><a href=\"search.php?kind={$kind}&keywords={$keywords}&page={$i}\">{$i}</a></li>";
	}
}
//尾
if ($page == $p || $p ==0)
{
    echo '<li class="disabled"><a href="#">&raquo;</a></li>';
}
else
{
    echo "<li><a href=\"search.php?kind={$kind}&keywords={$keywords}&page={($page+1)}\">&raquo;</a></li>";
}
echo "</ul>";


?>



</div>


	<nav class="navbar navbar-default navbar-fixed-bottom" role="navigation">
		<p class="navbar-text navbar-left">公开漏洞、知识库等数据来自于wooyun.org</p>
		<!--<p class="navbar-text navbar-right">written by grt1st</p> -->
	</nav>

</body>
</html>

