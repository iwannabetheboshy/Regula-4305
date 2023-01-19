<?php
	/*
		0 - выборка всех тестов
		1 - вставка нового теста
		2 - обновить тест
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
	require_once '..' . DIRECTORY_SEPARATOR . 'settings.php';

	if (isset($_GET['type']))
		$type = $_GET['type'];

	switch ($type)
	{
		case 0:		
			$SQL = "exec sp_sel_Tests @ShowAll = 1";
			$result = $config->query($SQL);
			die(json_encode($result));
			break;
		case 1:		
			$IdTrainer = $_POST['IdTrainer'];
			$TestName = $_POST['TestName'];
			$TestDescription = $_POST['TestDescription'];
			$MaxMinutesDuration = $_POST['MaxMinutesDuration'];

			if ($IdTrainer == 'NULL')
				$IdTrainer = NULL;

			$TestAvailable = 0;
			if (isset($_POST['TestAvailable']))
				$TestAvailable = 1;

			$UnsortQuestions = 0;
			if (isset($_POST['UnsortQuestions']))
				$UnsortQuestions = 1;

        	$SQL = "exec sp_ins_Tests  @IdTrainer = ?, @TestName = ?, @TestDescription = ?, @TestAvailable = ?, @MaxMinutesDuration = ?, @UnsortQuestions = ?";
        	$result = $config->QueryWithParams($SQL, [$IdTrainer, $TestName, $TestDescription, $TestAvailable, $MaxMinutesDuration, $UnsortQuestions]);
        	die(json_encode($result));
			break;
		case 2:
			$IdTest = $_POST['editIdTest'];
			$IdTrainer = $_POST['editIdTrainer'];
			$TestName = $_POST['editTestName'];
			$TestDescription = $_POST['editTestDescription'];
			$MaxMinutesDuration = $_POST['editMaxMinutesDuration'];

			if ($IdTrainer == 'NULL')
				$IdTrainer = NULL;

			$TestAvailable = 0;
			if (isset($_POST['editTestAvailable']))
				$TestAvailable = 1;

			$UnsortQuestions = 0;
			if (isset($_POST['editUnsortQuestions']))
				$UnsortQuestions = 1;

        	$SQL = "exec sp_upd_Tests  @IdTest = ?, @IdTrainer = ?, @TestName = ?, @TestDescription = ?, @TestAvailable = ?, @MaxMinutesDuration = ?, @UnsortQuestions = ?";
        	$result = $config->QueryWithParams($SQL, [$IdTest, $IdTrainer, $TestName, $TestDescription, $TestAvailable, $MaxMinutesDuration, $UnsortQuestions]);

        	die("Update Complete");
			break;	
		/*case 3:		
			$IdAdmin = $_POST['IdAdmin'];
			$SQL = "exec sp_del_AdminUser  @IdAdmin = ? ";
        	$result = $config->QueryWithParams($SQL, [$IdAdmin]);
        	die('Delete complete');			
			break;*/
		default:
			die("Access Deny");			
	}
	