<?php
	header('Content-type: text/html; charset=utf-8');
	define("AccessToFile",1);
	
	ini_set('display_errors',1);
	error_reporting(E_ALL);

	require_once '..\..\admin\settings.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>VFRTA - Тестирование</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="../../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../js/jquery-ui/jquery-ui.css">
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
  <h1>Тестирование</h1>
  <p>Российская таможенная академия</p>
</div>

<div class="container" style="text-align: center;">

    <div class="row">
      <div class="col-md-6">
        <div id="page-selection"></div>
      </div>
      <div class="col-md-6"> <!-- col-md-offset-3 -->
          <button type="button" class="btn btn-default" style="margin: 20px 0">Следующий вопрос</button>
      </div>
    </div>

    <div id="content">Dynamic Content goes here</div>
    </br>

</div>

</body>
</html>