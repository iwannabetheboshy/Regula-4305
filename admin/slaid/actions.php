<?php	
/*$type^
	0 - выбрать слайдшоу. Параметр: IdTrainer - код тренажера
	1 - добавить новый слайд. Параметры :  TrainerId - код тренажера, IsBegin (1,0) - для начала или для конца, SlideText - текст к слайду, SlidePosition - позиция слайда, SlideCaption - заголовок слайда
	2 - удалить слайд. Параметры: IdSlide - код слайда, ImagePath - имя файла;
	3 - обновить позицию слайда. Параметры: IdSlide - код слайда, SlidePosition - новая позиция слайда.
	4 - обновить слайд.
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
			$SQL = "exec [sp_sel_Slides] @IdTrainer = ?";			
			$result = $config->QueryWithParams($SQL, [$IdTrainer]);
			echo json_encode($result);			 
			break;
		case 1:
			$IdTrainer = $_POST['IdTrainer'];
			$IsBegin = $_POST['IsBegin'];
			$SlideText = $_POST['SlideText'];
			
			if (isset($_POST['SlidePosition']))
				$SlidePosition = $_POST['SlidePosition'];
			else 
				$SlidePosition = null;

			$SlideCaption = $_POST['SlideCaption'];

			$error = '';
			if ($_FILES['imagefile']['name'] == '') 
				$error .= 'Выберите файл';
			else 
			{
				$ext = mb_strtolower(pathinfo($_FILES['imagefile']['name'], PATHINFO_EXTENSION));
				if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'bmp' && $ext != 'gif') $error .= 'Недопустимый формат файла';
			}

			if ($error == '')
			{
				$SQL = "exec [sp_ins_Slide]  @IdTrainer=?, @IsBegin=?, @ImageExt=?, @SlideText=?, @SlidePosition=?, @SlideCaption=?";
				$result = $config->QueryWithParams($SQL, [$IdTrainer, $IsBegin, $ext, $SlideText, $SlidePosition, $SlideCaption]);
				
				$file = $result[0]["IdSlide"] . "." . $ext;

				$uploaddir = "../../images/slaid/";
				$uploadfile = $uploaddir . $file;

				echo $uploadfile; 

				if (!file_exists($uploaddir))
			    	mkdir($uploaddir);

				if (move_uploaded_file($_FILES['imagefile']['tmp_name'], $uploadfile)) 
				{
					echo "";
				} else {
	    			echo "Не удалось загрузить файл. Возможно проблемы с сетевым подключением или т.п. Проверьте ваше подключение и попробуйте снова.\n";
				}
			} else 
				echo $error;
			break;
		case 2:
			$IdSlide = $_POST['IdSlide'];
			$SQL = "exec [sp_del_Slide] @IdSlide = ?";			
			$result = $config->QueryWithParams($SQL, [$IdSlide]);
			echo json_encode($result);
			unlink("../../images/slaid/" . $_POST['ImagePath']); //имя файла;	 
			break;
		case 3:
			$IdTrainer = $_POST['IdTrainer'];

			$Slides = $_POST['Slides'];
			//print_r($Slides);

			foreach ($Slides as $key => $value) {
				//print(';'.$key.'=>'.$value.';');
				$SQL = "exec [sp_upd_SlidePosition] @IdSlide = ? , @SlidePosition = ?";			
				$result = $config->QueryWithParams($SQL, [$value, $key+1]);
			}
			echo ('Order has been updated');

		/*	$IdSlide = $_POST['IdSlide'];
			$SlidePosition = $_POST['SlidePosition'];
			$SQL = "exec [sp_upd_SlidePosition] @IdSlide = ? , @SlidePosition = ?";			
			$result = $config->QueryWithParams($SQL, [$IdSlide, $SlidePosition]);
			echo json_encode($result);*/

			break;
		case 4:  //Slides - post
			$IdSlide = $_POST['editIdSlide'];
			$SlideText = $_POST['editSlideText'];
			$SlideCaption = $_POST['editSlideCaption'];

			$SQL = "exec [sp_upd_Slide] @IdSlide = ? , @SlideText = ? , @SlideCaption = ?";			
			$result = $config->QueryWithParams($SQL, [$IdSlide, $SlideText, $SlideCaption]);
			echo json_encode($result);

			$error = '';			
			if ($_FILES['editImagefile']['name'] == '') 
				$error .= 'Выберите файл';
			else 
			{
				$ext = mb_strtolower(pathinfo($_FILES['editImagefile']['name'], PATHINFO_EXTENSION));
				if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'bmp' && $ext != 'gif') $error .= 'Недопустимый формат файла';
			}

			if ($error == '')
			{
				$SQL = "exec [sp_upd_SlideFile]  @IdSlide=?, @Ext=?";
				$result = $config->QueryWithParams($SQL, [$IdSlide, $ext]);
				
				$file = $result[0]["IdSlide"] . "." . $ext;

				$uploaddir = "../../images/slaid/";
				$uploadfile = $uploaddir . $file;

				echo $uploadfile;

				if (!file_exists($uploaddir))
			    	mkdir($uploaddir);

				if (move_uploaded_file($_FILES['editImagefile']['tmp_name'], $uploadfile)) 
				{
					echo "ok";
				} else {
	    			echo "Не удалось загрузить файл. Возможно проблемы с сетевым подключением или т.п. Проверьте ваше подключение и попробуйте снова.\n";
				}
			} else 
				echo $error;
			break;	
		default:
			echo("Access Deny");			
	}

?>