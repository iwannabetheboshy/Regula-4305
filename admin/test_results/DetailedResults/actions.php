<?php
	/*
		0 - выборка
		1 - вставка
		2 - обновить
		3 - удалить
	*/
	if (!isset($_SESSION))
		session_start();	
	if (!isset($_SESSION['SessionId']))
		die('Access Deny');
	if (!( (isset( $_SESSION['isAdmin'])) && ($_SESSION['isAdmin'] == 1) )) {
  		header("Location: ../auth.php ");
  		die();
	}
	
	ini_set('display_errors',1);
	error_reporting(E_ALL);

	define("AccessToFile", 1);
	require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'settings.php';

	if (isset($_GET['type']))
		$type = $_GET['type'];

	switch ($type)
	{
		case 0: 
			/*Общая информация о сеансе тестирования*/
			$IdSession = $_POST['IdSession'];
			$SQL = "exec [sp_getTestsResults] @IdSession = ?";
			$result = $config->QueryWithParams($SQL, [$IdSession]);
			die(json_encode($result));
			break;
		case 1:
			$IdSession = $_POST['IdSession'];
			$SQL = "exec [sp_getExtendTestResults] @IdSession = ?";
			$result = $config->QueryWithParams($SQL, [$IdSession]);
			die(json_encode($result));
			break;
		case 2:
			$IdSession = $_POST['IdSession'];
			$IdQuestion = $_POST['IdQuestion'];
			$SQL = "exec sp_getAnswersTestResults @IdSession = ?, @IdQuestion = ?";
			$result = $config->QueryWithParams($SQL, [$IdSession, $IdQuestion]);
			die(json_encode($result));
			break;
		default:
			die("Access Deny");			
	}
	