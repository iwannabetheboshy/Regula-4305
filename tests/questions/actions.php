<?php 
	session_start();	
	if (!isset($_SESSION['SessionId']))
		die('Access Deny');
	ini_set('display_errors',1);
	define("AccessToFile", 1);
	require_once '..\..\admin\settings.php';

	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {	
		$type= $_POST['type'];
		switch($type) {
			case 0:
				$SQL = "exec sp_GetTestQuestions @IdSession = ?";     
  				$result = $config->QueryWithParams($SQL, [$_SESSION['IdTestSession']]);
				
  				foreach ($result as $key => $value) {
  					$SQL1 = "exec sp_GetQuestionResponseOptions @IdQuestion = ?";     
  					$result[$key]['answers'] = $config->QueryWithParams($SQL1, [$value['IdQuestion']]);
  				}

				echo json_encode($result);
				break;
			case 1:
				$SQL = "exec sp_SetTestAnswer @IdSession = ?, @IdAnswer = ?, @isSet = ?";     
  				$result = $config->QueryWithParams($SQL, [$_SESSION['IdTestSession'], 
  					$_POST['IdAnswer'], $_POST['isSet']]);
  				echo 'ok';
				break;
			default: 
				die('Access Deny');
		}		
	} else
	die('Access Deny');
?>
