<?php	
	session_start();
	if (!isset($_SESSION['SessionId']))
		die('Access Deny');

	if (isset($_GET['TrainerName']))
	{
		$_SESSION['TrainerName'] = $_GET['TrainerName'];
		die();
	}

	  /*Проверка авторизации*/
  	if (!( (isset( $_SESSION['isAdmin'])) && ($_SESSION['isAdmin'] == 1) )) {
	    header("Location: auth.php ");
	    die();
  	}	
	
	ini_set('display_errors',1);
	error_reporting(E_ALL);

	define("AccessToFile", 1);
	require_once 'settings.php';

	$SQL = "exec sp_sel_TrainerMachines";
	$result = $config->query($SQL);

	echo json_encode($result);
?>