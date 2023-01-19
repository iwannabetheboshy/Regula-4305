<?php 
	/*Operation:
		0 - выбрать все объекты контроля для выбранного тренажера
		1 - добавить файл сохраненный пользователем в базу данных
		3 - выбрать изображения сохраненные студентом в базе
		4 - внести данные в лог действий пользователей
	*/

	session_start();	
	if (!isset($_SESSION['SessionId']))
		die('Access Deny');
	ini_set('display_errors',1);
	define("AccessToFile", 1);
	require_once '..' . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR .'admin' . DIRECTORY_SEPARATOR  . 'settings.php';


	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {	
		$type= $_POST['type'];
		if (!isset($type))
			$type = $_GET['type'];
		switch($type) {
			case 0:
				$SQL = "exec sp_sel_TrainerImage ?";
				$params = array($_SESSION['IdTrainer']);
				echo json_encode($config->QueryWithParams($SQL,$params));
				break;
			case 1:
				$SQL = "exec sp_ins_TrainerSavedImages @IdTrainer = ? , @IdStudUser = ? , @IdSession = ?, @ImageExt = ? ";
				$params = array($_SESSION['IdTrainer'], $_SESSION['IdUser'] , $_SESSION['SystemSessionId'], 'png');
				$result = $config->QueryWithParams($SQL,$params);

				$img = $_POST['img_data'];
				$img = str_replace('data:image/png;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				$fileData = base64_decode($img);
				//saving
				
				$fileName = '../../images/StudentImages/' . $result[0]['IdSavedImage'] .  '.png';
				file_put_contents($fileName, $fileData);

				echo json_encode($result[0]);
				break;
			case 3:
				$SQL = "exec [sp_sel_TrainerSavedImages] @IdTrainer = ?";
				$params = array($_SESSION['IdTrainer']);
				echo json_encode($config->QueryWithParams($SQL,$params));
				break;
			case 4:
				$SQL = "exec sp_ins_StudentActivitiHist @IdSession = ? , @IdAction = ? , @IdImage = ? , @ImagePath = ? ";
				$params = array($_SESSION['SystemSessionId'], json_decode($_POST['data'])->IdAction , json_decode($_POST['data'])->IdImage, json_decode($_POST['data'])->ImagePath);

				echo json_encode($config->QueryWithParams($SQL,$params));
				break;
			default: 
				die('Access Deny');
		}		
	} else
	die('Access Deny');
?>
