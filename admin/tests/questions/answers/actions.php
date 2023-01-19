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

	require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'settings.php';

	if (isset($_GET['type']))
		$type = $_GET['type'];

	switch ($type)
	{
		case 0: 
			$IdQuestion = $_POST['IdQuestion'];
			$SQL = "exec sp_sel_TestAnswers @IdQuestion = ?";
			$result = $config->QueryWithParams($SQL, [$IdQuestion]);
			die(json_encode($result));
			break;
		case 1:	
			$IdQuestion = $_POST['addIdQuestion'];
			$AnswerText = $_POST['addAnswerText'];

			$IsCorrect = 0;
			if (isset($_POST['addIsCorrect']))
				$IsCorrect = 1;

        	$SQL = "exec sp_ins_TestAnswer @IdQuestion = ?, @IsCorrect = ?, @AnswerText = ?";
        	$result = $config->QueryWithParams($SQL, [$IdQuestion, $IsCorrect, $AnswerText]);
        	die("Insert");
			break;	
		case 2:
			$IdQuestion = $_POST['IdQuestion'];
			$IdAnswer = $_POST['IdAnswer'];
			$AnswerText = $_POST['AnswerText'];

			$IsCorrect = 0;
			if (isset($_POST['IsCorrect']))
				$IsCorrect = 1;

        	$SQL = "exec sp_upd_TestAnswer  @IdAnswer = ?, @IdQuestion = ?, @IsCorrect = ?, @AnswerText = ?";
        	$result = $config->QueryWithParams($SQL, [$IdAnswer, $IdQuestion, $IsCorrect, $AnswerText]);
        	die(json_encode($result));
		case 3:		
			$IdAnswer = $_POST['IdAnswer'];
			$SQL = "exec sp_del_TestAnswers  @IdAnswer = ? ";
        	$result = $config->QueryWithParams($SQL, [$IdAnswer]);
        	die('Delete complete');	
		default:
			die("Access Deny");			
	}
	