<?php	
/*$type^
	0 - выбрать сессии. Параметр: IdTrainer - код тренажера
	1 - выбрать подробные данные по сессии. Параметр:  IdSession - код сессии
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
			//print_r($_POST);
			$IdTrainer = $_POST['IdTrainer'];
			$StudentName = $_POST['StudentName'];
			$DateFrom = json_decode($_POST['DateFrom'])->value;
			$DateTo = json_decode($_POST['DateTo'])->value;
			$ShowOnlyAlert = $_POST['ShowOnlyAlert'];


			$SQL = "exec [sp_sel_TrainerSessions] @IdTrainer = ?, @StudentName = ?, @ShowAlertOnly = ?, @DateFrom = ?, @DateTo = ?";			
			$result = $config->QueryWithParams($SQL, [$IdTrainer, $StudentName, $ShowOnlyAlert, $DateFrom, $DateTo]);
			
			$year = date_parse($result[0]['LoginDate'])['year'];
			$month = date_parse($result[0]['LoginDate'])['month'];
			$day = date_parse($result[0]['LoginDate'])['day'];
			$i = 0;
			$j = 0;
			
			if ($result != null) {
				foreach ($result as $key => $value) {
					if (!($year == date_parse($value['LoginDate'])['year'] && $month == date_parse($value['LoginDate'])['month'] && $day == date_parse($value['LoginDate'])['day']))
					{
						$i=$i+1;
						$j=0;
						$year = date_parse($value['LoginDate'])['year'];
						$month = date_parse($value['LoginDate'])['month'];
						$day = date_parse($value['LoginDate'])['day'];
					}
					
					$date = new DateTime();
					$newResult[$i]['LoginDate'] =  $day . '.' . $month . '.' . $year;
					$newResult[$i]['Sessions'][$j] = $value;
					$j = $j+1;
				}
				die( json_encode($newResult) );
			}
			break;
		case 1:
			$IdSession = $_POST['IdSession'];
			$SQL = "exec [sp_sel_SessionsInfo] @IdSession = ?";			
			$result = $config->QueryWithParams($SQL, [$IdSession]);
			die(json_encode($result));
			break;
		default:
			echo("Access Deny");			
	}

?>