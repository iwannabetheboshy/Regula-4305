<?php	
/*$type
	0 - выбрать объекты контроля, параметр  - код тренажера
	1 - выбрать превью 
	2 - удалить объект контроля
	3 - добавить новый объект контроля
	4 - удалить тип объекта контроля
	5 - добавить новый тип объекта контроля
	*/
	if (!isset($_SESSION))
		session_start();
	if (!isset($_SESSION['SessionId']))
		die('Access Deny');

	/*Проверка авторизации*/
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
			$IdTrainer = $_POST['IdTrainer'];
			$SQL = "exec [sp_sel_TrainerSavedImages] @IdTrainer = ?";
			//$params = array($_SESSION['IdTrainer']);
			echo json_encode($config->QueryWithParams($SQL, array($IdTrainer)));
			break;
		case 1:
			$IdSavedImage = $_POST['IdSavedImage'];
			$SQL = "exec sp_del_TrainerSavedImages @IdSavedImage=?";
			$result = $config->QueryWithParams($SQL, [$IdSavedImage]);
			echo json_encode($result);
			unlink("../../images/StudentImages/" . $_POST['ImagePath']);
			break;
		default:
			echo("Access Deny");			
	}

?>