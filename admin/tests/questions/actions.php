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
			$IdTest = $_POST['IdTest'];
			$SQL = "exec sp_sel_TestQuestions @IdTest = ?";
			$result = $config->QueryWithParams($SQL, [$IdTest]);
			die(json_encode($result));
			break;
		case 1:	
			$IdTest = $_POST['IdTest'];
			$TestCaption = $_POST['TestCaptionAdd'];
			$TestText = $_POST['TestTextAdd'];
			$QuestionBalls = $_POST['QuestionBallsAdd'];

			$SkipQuestion = 0;
			if (isset($_POST['SkipQuestionAdd']))
				$SkipQuestion = 1;

			$MultipleAnswersAllow = 0;
			if (isset($_POST['MultipleAnswersAllowAdd']))
				$MultipleAnswersAllow = 1;

			$error = '';
			if ($_FILES['ImagePath']['name'] == '')
			{
        		$SQL = "exec sp_ins_TestQuestion @IdTest = ?, @TestCaption = ?, @TestText = ?, @SkipQuestion = ?, @QuestionBalls = ?, @MultipleAnswersAllow = ?";
        		$result = $config->QueryWithParams($SQL, [$IdTest, $TestCaption, $TestText, $SkipQuestion, $QuestionBalls, $MultipleAnswersAllow]);
				$error .= 'Обновлены данные';
			}
			else 
			{
				$ext = mb_strtolower(pathinfo($_FILES['ImagePath']['name'], PATHINFO_EXTENSION));
				if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'bmp' && $ext != 'gif') $error .= 'Недопустимый формат файла';
			}

			if ($error == '')
			{
        		$SQL = "exec sp_ins_TestQuestion @IdTest = ?, @ImagePath = ?, @TestCaption = ?, @TestText = ?, @SkipQuestion = ?, @QuestionBalls = ?, @MultipleAnswersAllow = ?";
        		$result = $config->QueryWithParams($SQL, [$IdTest, $ext, $TestCaption, $TestText, $SkipQuestion, $QuestionBalls, $MultipleAnswersAllow]);

				$file = $result[0]["IdQuestion"] . "." . $ext;

				$uploaddir = "../../../images/questions/";
				$uploadfile = $uploaddir . $file; 

				if (!file_exists($uploaddir))
			    	mkdir($uploaddir);

				if (move_uploaded_file($_FILES['ImagePath']['tmp_name'], $uploadfile)) 
				{
					echo "Объект контроля был успешно добавлен";
				} else {
	    			echo "Не удалось загрузить файл. Возможно проблемы с сетевым подключением или т.п. Проверьте ваше подключение и попробуйте снова.\n";
	    			echo $uploadfile; 
				}
			} else 
				echo $error;

        	die("Insert");
			break;	
		case 2:
			$IdQuestion = $_POST['IdQuestion'];
			$TestCaption = $_POST['TestCaption'];
			$TestText = $_POST['TestText'];
			$QuestionBalls = $_POST['QuestionBalls'];

			$SkipQuestion = 0;
			if (isset($_POST['SkipQuestion']))
				$SkipQuestion = 1;

			$MultipleAnswersAllow = 0;
			if (isset($_POST['MultipleAnswersAllow']))
				$MultipleAnswersAllow = 1;

			$error = '';
			if ($_FILES['ImagePath']['name'] == '') 
			{
				$SQL = "exec sp_upd_TestQuestion  @IdQuestion = ?, @TestCaption = ?, @TestText = ?, @SkipQuestion = ?, @QuestionBalls = ?, @MultipleAnswersAllow = ?";
        		$result = $config->QueryWithParams($SQL, [$IdQuestion, $TestCaption, $TestText, $SkipQuestion, $QuestionBalls, $MultipleAnswersAllow]);
        		$error = 'Обновлены данные';
			}
			else 
			{
				$ext = mb_strtolower(pathinfo($_FILES['ImagePath']['name'], PATHINFO_EXTENSION));
				if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'bmp' && $ext != 'gif') $error .= 'Недопустимый формат файла';
			}

			if ($error == '')
			{
				$SQL = "exec sp_upd_TestQuestion  @IdQuestion = ?, @ImagePath = ?, @TestCaption = ?, @TestText = ?, @SkipQuestion = ?, @QuestionBalls = ?, @MultipleAnswersAllow = ?";
        		$result = $config->QueryWithParams($SQL, [$IdQuestion, $ext, $TestCaption, $TestText, $SkipQuestion, $QuestionBalls, $MultipleAnswersAllow]);

				$file = $result[0]["IdQuestion"] . "." . $ext;

				$uploaddir = "../../../images/questions/";
				$uploadfile = $uploaddir . $file; 

				if (!file_exists($uploaddir))
			    	mkdir($uploaddir);

				if (move_uploaded_file($_FILES['ImagePath']['tmp_name'], $uploadfile)) 
				{
					echo "Объект контроля был успешно добавлен";
				} else {
	    			echo "Не удалось загрузить файл. Возможно проблемы с сетевым подключением или т.п. Проверьте ваше подключение и попробуйте снова.\n";
	    			echo $uploadfile; 
				}
			} else 
				echo $error;
			break;
		case 3:		
			$IdQuestion = $_POST['IdQuestion'];
			$SQL = "exec sp_del_TestQuestion  @IdQuestion = ? ";
        	$result = $config->QueryWithParams($SQL, [$IdQuestion]);
        	unlink("../../../images/questions/" . $_POST['ImagePath']);
        	die('Delete complete');
		default:
			die("Access Deny");			
	}
	