<?php 
	session_start();	
	if (!isset($_SESSION['SessionId']))
		die('Access Deny');
	ini_set('display_errors',1);
	define("AccessToFile", 1);
	require_once '\..\admin\settings.php';

	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {	
		$type= $_POST['type'];
		switch($type) {
			case 0:
				if (!isset($_SESSION['IdUser']))
					$_SESSION['IdUser'] = NULL;
				
				$SQL = "exec sp_RegisterOnTest @IdStudUser = ?, @UserName = ?, @IdTest = ?";

				$params = array($_SESSION['IdUser'], $_SESSION['StudentName'], $_POST['IdTest']);

				//echo $SQL;
				//print_r($params);
				
				$result = $config->QueryWithParams($SQL,$params);
				$_SESSION['IdTestSession'] = $result[0]['IdTestSession'];
				echo 'ok';
				break;
			case 1:
				break;
			default: 
				die('Access Deny');
		}		
	} else
	die('Access Deny');
?>
