<?php
	header('Content-type: text/html; charset=utf-8');
	define("AccessToFile",1);
	
	if (!isset($_SESSION))
    	session_start();
	
	$_SESSION['SessionId']= session_id();

  if (isset($_GET['name']))
	   $_SESSION['StudentName'] = $_GET['name'];

	if (!defined("AccessToFile"))
	{
		die("Access Deny");
	}

	ini_set('display_errors',1);
	error_reporting(E_ALL);

	require_once '..' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'settings.php';
  
  $SQL = "exec [sp_sel_Tests]";     
  $result = $config->QueryWithParams($SQL, []);

    if ($result == null)
    	header("Location: ../"); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>VFRTA - Тестирование</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/jquery-ui/jquery-ui.css">
  <script src="../js/jquery-ui/external/jquery/jquery.js"></script>
  <script src="../js/jquery-ui/jquery-ui.js"></script>
  <script src="main.js"></script>
</head>
<body>

 <nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="../">VFRTA</a>
    </div>
    <ul class="nav navbar-nav">
      <li><a href="../">Тренажёры</a></li>
      <li class="active"><a href="#">Тестирование</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?=$_SESSION['StudentName'] ?></a></li>
      <li><a href="../"><span class="glyphicon glyphicon-log-in"></span> Выход</a></li>
    </ul>
  </div>
</nav>

<div class="jumbotron text-center">
  <h1>Тестирование</h1>
  <p>Российская таможенная академия</p>
</div>

<div class="container">
   <p>Выберите тест для прохождения...</p>
   <div class="row">
		<div class="list-group">
		  <?php foreach ($result as $test) { ?>
		  <a href="#" class="list-group-item" data-id="<?=$test['IdTest'] ?>">
		    <h4 class="list-group-item-heading"><?=$test['TestName'] ?></h4>
		    <p class="list-group-item-text"><?=$test['TestDescription'] ?></p>
		  </a>
		  <?php } ?>
		</div>
    </div>
</div>

</body>
</html>