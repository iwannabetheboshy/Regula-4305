<?php	
/*$type
	0 - выбрать объекты контроля, параметр  - код тренажера
	1 - выбрать превью 
	2 - удалить объект контроля
	3 - добавить новый объект контроля
	4 - удалить тип объекта контроля
	5 - добавить новый тип объекта контроля
	6 - получаем список дополнительных полей для объекта контроля (поля которые нужны только в указанном тренажере)
	7 - обновление объекта сканирования
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
			$SQL = "exec [sp_sel_TrainerImage] ?";			
			$result = $config->QueryWithParams($SQL, [$IdTrainer]);
			echo json_encode($result);			 
			break;
		case 1:	
			$SQL = "exec [sp_sel_Previews]";
			$result = $config->query($SQL);
			echo json_encode($result);			 
			break;
		case 2:		
			$ImageId = $_POST['IdImage'];
			$SQL = "exec sp_del_TrainerImage @IdImage  = ? 	";
			$result = $config->QueryWithParams($SQL, [$ImageId]);
			echo json_encode($result);
			unlink("../../images/scans/" . $_POST['ImagePath']);	 
			break;
		case 3:
			$IdTrainer = $_POST['IdTrainer'];
			$IdPreview = $_POST['IdPreview'];
			$Name = $_POST['Name'];

			
			if ($IdTrainer == 5) //Добавляем вещества без изображения (Для Кербера) 
			{
				$SQL = "exec sp_ins_TrainerImage @IdTrainer = ?,  @Filename = ?, @IdPreview = ?, @Name = ? ";   
				$result = $config->QueryWithParams($SQL, [$IdTrainer, NULL, $IdPreview, $Name]);
				//Получаем список дополнительных полей для тренажера
				$NewId = $result[0]["Id"];
				$SQL = " select * from TrainerImagesAdditionalFieldsDef WHERE IdTrainer = ? ";      
	            $result = $config->QueryWithParams($SQL, [$IdTrainer]);
	            foreach ($result  as $key => $value) {
	            	if (isset($_POST['ed'.$value["FieldDBName"]])) {
	            		$SQL = "INSERT INTO [dbo].[TrainerImagesAddFields] ([IdAdditionalField],[FieldValue],[IdImage]) VALUES (?,?,?)";   
	            		if ($value["FieldType"] == "BIT")
	            			{
	            				if (isset($_POST['ed'.$value["FieldDBName"]]) && !empty($_POST['ed'.$value["FieldDBName"]]))
	            					$RealVal = 1;
	            				else
	            					$RealVal = 0;
	            			}
	            		else 
	            			$RealVal = $_POST['ed'.$value["FieldDBName"]];
						$result = $config->QueryWithParams($SQL, [$value['IdAdditionalField'], $RealVal, $NewId]);
					}
	            }

				die();
			}

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
				$SQL = "exec sp_ins_TrainerImage @IdTrainer = ?,  @Filename = ?, @IdPreview = ?, @Name = ? ";   
				$result = $config->QueryWithParams($SQL, [$IdTrainer, $ext, $IdPreview, $Name]);

				$file = $result[0]["Id"] . "." . $ext;

				$uploaddir = "../../images/scans/";
				$uploadfile = $uploaddir . $file; 

				if (!file_exists($uploaddir))
			    	mkdir($uploaddir);

				if (move_uploaded_file($_FILES['imagefile']['tmp_name'], $uploadfile)) 
				{
					echo "Объект контроля был успешно добавлен";
				} else {
	    			echo "Не удалось загрузить файл. Возможно проблемы с сетевым подключением или т.п. Проверьте ваше подключение и попробуйте снова.\n";
				}

				//Получаем список дополнительных полей для тренажера
				$NewId = $result["Id"];
				$SQL = " select * from TrainerImagesAdditionalFieldsDef WHERE IdTrainer = ? ";      
	            $result = $config->QueryWithParams($SQL, [$IdTrainer]);
	            foreach ($result  as $key => $value) {
	            	if (isset($_POST['ed'.$value["FieldDBName"]])) {
	            		$SQL = "INSERT INTO [dbo].[TrainerImagesAddFields] ([IdAdditionalField],[FieldValue],[IdImage]) VALUES (?,?,?)";   
	            		if ($value["FieldType"] == "BIT")
	            			{
	            				if (isset($_POST['ed'.$value["FieldDBName"]]) && !empty($_POST['ed'.$value["FieldDBName"]]))
	            					$RealVal = 1;
	            				else
	            					$RealVal = 0;
	            			}
	            		else 
	            			$RealVal = $_POST['ed'.$value["FieldDBName"]];
						$result = $config->QueryWithParams($SQL, [$value['IdAdditionalField'], $RealVal, $NewId]);
					}
	            }
			} else 
				echo $error;
			break;
		case 4:
			$IdPreview = $_POST['IdPreview'];
			$SQL = "exec sp_del_Previews @IdPreview = ?";
			$result = $config->QueryWithParams($SQL,[$IdPreview]);
			unlink("../../images/preview/" . $_POST['ImagePath']);	 
			break;
		case 5:
			$PreviewName = $_FILES['imagefile']['name'];

			$error = '';
			if ($_FILES['imagefile']['name'] == '') 
				$error .= 'Выберите файл';
			else 
			{
				$ext = mb_strtolower(pathinfo($_FILES['imagefile']['name'], PATHINFO_EXTENSION));
				if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'bmp' && $ext != 'gif') $error .= 'Недопустимый формат файла';
			}
			
			$SQL = "exec sp_ins_Previews @PreviewName=?";
			$result = $config->QueryWithParams($SQL,[$ext]);

			$file = $result[0]["IdPreview"] . "." . $ext;
			$uploaddir = "../../images/preview/";
			$uploadfile = $uploaddir . $file; 

			if (move_uploaded_file($_FILES['imagefile']['tmp_name'], $uploadfile)) 
			{
				echo "Тип объекта контроля был успешно добавлен";
			} else {
    			echo "Не удалось загрузить файл. Возможно проблемы с сетевым подключением или т.п. Проверьте ваше подключение и попробуйте снова.\n";
			}	
			break;
		case 6:
			$IdTrainer = $_POST['IdTrainer']; 
			$SQL = "SELECT * FROM TrainerImagesAdditionalFieldsDef WHERE IdTrainer = ? ORDER BY IdAdditionalField ASC";
            $result = $config->QueryWithParams($SQL, [$IdTrainer]); 
            echo json_encode($result);         
			break;
		case 7:
			print_r($_POST);			
			$IdImage = $_POST['IdImage'];			
			if ($_FILES['imagefile']['name'] == '') 
				$ext = NULL;
			else 
			{/*Файл*/
				$ext = mb_strtolower(pathinfo($_FILES['imagefile']['name'], PATHINFO_EXTENSION));
				if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'bmp' && $ext != 'gif') $error .= 'Недопустимый формат файла';

				$file = $IdImage . "." . $ext;

				$uploaddir = "../../images/scans/";
				$uploadfile = $uploaddir . $file; 

				if (!file_exists($uploaddir))
			    	mkdir($uploaddir);

				if (move_uploaded_file($_FILES['imagefile']['tmp_name'], $uploadfile)) 
				{
					echo "Объект контроля был успешно добавлен";
				} else {
	    			echo "Не удалось загрузить файл. Возможно проблемы с сетевым подключением или т.п. Проверьте ваше подключение и попробуйте снова.\n";
				}
			}			

			/*Основные параметры*/
			$SQL = "EXEC [sp_upd_TrainerImage] @IdImage=?, @Filename=?, @IdPreview=?, @Name=?";
			$result = $config->QueryWithParams($SQL, [$IdImage, $ext, $_POST['IdPreview'], $_POST['Name'] ]);

			/*Дополнительные параметры*/
			$SQL = " SELECT * FROM TrainerImagesAdditionalFieldsDef WHERE IdTrainer = ? ORDER BY IdAdditionalField ASC";
		    $result = $config->QueryWithParams($SQL, [$_POST['IdTrainer']]);	    
		    if (isset($result) && $result != NULL)
		    	foreach ($result  as $key => $value) {
	            	if ((isset($_POST['ed'.$value["FieldDBName"]])) || ($value["FieldType"] == "BIT")) {
	            		$SQL = "exec sp_upd_AdlTrainerImageFlds @IdImage = ?, @IdAdditionalField = ?, @FieldValue = ? ";
	            		if ($value["FieldType"] == "BIT")
	            			{
	            				if (isset($_POST['ed'.$value["FieldDBName"]]) && !empty($_POST['ed'.$value["FieldDBName"]]))
	            					$RealVal = 1;
	            				else
	            					$RealVal = 0;
	            			}
	            		else 
	            			$RealVal = $_POST['ed'.$value["FieldDBName"]];
						$result = $config->QueryWithParams($SQL, [$IdImage, $value["IdAdditionalField"], $RealVal ]);
					}
	            }		      
			break;
		default:
			echo("Access Deny");			
	}

?>