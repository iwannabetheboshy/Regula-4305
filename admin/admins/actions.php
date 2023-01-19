<?php
	/*
		0 - выборка всех админов
		1 - вставка нового пользователя - администратора системы, параметры: login - логин, pwd - пароль, pwd2 - подтверждение пароля
		2 - обновить пароль выбранного администратора
		3 - удалить пользователя - администратора, параметры: IdAdmin
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
			$SQL = "exec sp_sel_AdminUsers";
			$result = $config->query($SQL);
			die(json_encode($result));
			break;
		case 1:		
			$login = $_POST['login'];
			$pwd = $_POST['pwd'];
        	$SQL = "exec sp_ins_AdminUser  @UserName = ?, @Password = ?";
        	$result = $config->QueryWithParams($SQL, [$login, $pwd]);
        	die(json_encode($result));
			break;
		case 2:		
			$IdAdmin = $_POST['IdAdmin'];
			$pwd = $_POST['editpwd'];
        	$SQL = "exec sp_upd_AdminUser  @IdAdmin = ?, @Password = ?";
        	$result = $config->QueryWithParams($SQL, [$IdAdmin, $pwd]);
        	die("Update Complete");
			break;	
		case 3:		
			$IdAdmin = $_POST['IdAdmin'];
			$SQL = "exec sp_del_AdminUser  @IdAdmin = ? ";
        	$result = $config->QueryWithParams($SQL, [$IdAdmin]);
        	die('Delete complete');			
			break;
		default:
			die("Access Deny");			
	}
	