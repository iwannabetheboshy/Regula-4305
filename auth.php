<?php
  
  /*$type
    0 - Вход в систему как студент, параметры IdTrainer, UserName
  */
  $mainpage = "index.htm";
  
  if (!isset($_SESSION))
    session_start();

  /*Удаляем переменые сессии на случай, если это администратор перешел в систему тренажеров, чтобы потом студент не мог зайти обратно под админом*/
  foreach ($_SESSION as $key => $value) {
      unset($value);
      unset($_SESSION[$key]);
    }

	$_SESSION['SessionId'] = session_id();
  define("AccessToFile", 1);
    
	//Эту проверку вставить во все php к которым нужен прямой доступ 
	if (!defined("AccessToFile"))
	{
		die("Access Deny");
	}

  require_once 'admin' . DIRECTORY_SEPARATOR . 'settings.php';

  if (isset($_GET['type'])) {
    $type = $_GET['type'];
  } else  {
    header("Location: ". $mainpage);
  }

  switch ($type)
  {
    case 0: 
      if (!isset($_POST['id']) || !isset($_POST['fio'])) {
        header("Location: ".$mainpage);
      } else {
        $IdTrainer = $_POST['id'];
        $StudentName = $_POST['fio'];
        $SQL = "exec sp_RegisterSession @UserName = ?, @IdTrainer = ?";
        $result = $config->QueryWithParams($SQL, [$StudentName, $IdTrainer]);
      
        /*Сохраняем все данные в сессию*/
        $_SESSION['IdTrainer'] = $IdTrainer;
        $_SESSION['StudentName'] =  $StudentName;
        $_SESSION['SystemSessionId'] = $result[0]['IdSession'];
        $_SESSION['IdUser'] = $result[0]['IdStudUser'];
        $_SESSION['MachineUrl'] = $result[0]['MachineUrl'];
        $_SESSION['MachineName'] = $result[0]['MachineName'];
        

        /*Перенаправляем на страницу тренажера*/
        header("Location: ".$result[0]['MachineUrl']);
        //print_r($_SESSION);
      }
      break;
      case 1:
        $SQL = "exec sp_sel_TrainerMachines";
        $result = $config->QueryWithParams($SQL, []);
        echo(json_encode($result));
      break;
    default:
      die('Access Deny');
  }
