<?php
	header('Content-type: text/html; charset=utf-8');
	define("AccessToFile",1);
	
	ini_set('display_errors',1);
	error_reporting(E_ALL);

	require_once '..\..\admin\settings.php';

  $SQL = "exec getTestResults @IdSession = ?";     
  $result = $config->QueryWithParams($SQL, [$_SESSION['IdTestSession']]);

  //print_r($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>VFRTA - Тестирование</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="../../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../js/jquery-ui/jquery-ui.css">
  <style>
     .letter {
        color: red; /* Цвет символа */
     }     
  </style>

  <script src="../../js/jquery-ui/external/jquery/jquery.js"></script>
  <script src="../../js/jquery-ui/jquery-ui.js"></script>
  <script src="../../js/jquery.bootpag.min.js"></script>
  <script src="main.js"></script>
</head>
<body>

 <nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="../../">VFRTA</a>
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
  <h1>Результат тестирования</h1>
  <p>Российская таможенная академия</p>
</div>

<div class="container" style="text-align: center;">
<h1><?=$_SESSION['StudentName'] ?></h1>
<h3>ваш результат за тест</h3>
<h1><?=$result[0]['TestName'] ?></h1>
<h3>составляет</h3>
<h1 class="letter"><?=$result[0]['ResultBalls'] ?> баллов из 100</h1>

</div>

</body>
</html>