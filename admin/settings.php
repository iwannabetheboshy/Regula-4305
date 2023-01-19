<?php

if (!defined("AccessToFile")) {
	die("Access Deny1");
}
if (!isset($_SESSION))
	session_start();
if (!isset($_SESSION['SessionId']))
	die('Access Deny2');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$config = new config();

class config
{
	private $server = "151.248.121.185, 1433";
	private $conn_string = array("Database" => "VFRTATrainers", "UID" => "vfrta", "PWD" => "NlQgbvToUDxMMFVmtM3J", "CharacterSet" => "UTF-8", "ReturnDatesAsStrings" => true);

	function ParseInt($s)
	{
		$s = preg_replace('/[^\-\d]*(\-?\d*).*/', '$1', $s);
		return $s;
	}

	/*************************
	 * выполнение SQL-запроса
	 ************************/
	function query($SQL)
	{
		$conn = sqlsrv_connect($this->server, $this->conn_string);
		if (!$conn) die($this->log_errors(sqlsrv_errors()));
		$result = sqlsrv_query($conn, $SQL);
		if (!$result) {
			$config->log($SQL);
			die($this->log_errors(sqlsrv_errors()));
		}

		while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) $out[] = $row;

		sqlsrv_free_stmt($result);
		sqlsrv_close($conn);

		if (isset($out) && !$out == null) return $out;
		//return false;
	}

	function QueryWithParams($SQL, $params)
	{
		$conn = sqlsrv_connect($this->server, $this->conn_string);
		if (!$conn) die($this->log_errors(sqlsrv_errors()));

		$result = sqlsrv_query($conn, $SQL, $params);
		if (!$result) {
			//$config->log($SQL);
			print_r(sqlsrv_errors());
			die($this->log_errors(sqlsrv_errors()));
		}

		while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
			$out[] = $row;
		}

		sqlsrv_free_stmt($result);
		sqlsrv_close($conn);

		if (isset($out) && !$out == null) return $out;
		//return false;
	}


	/********************************************************
	 * вывод текста в лог
	 *
	 * имя файла для лога можно передавать вторым параметром
	 * 
	 * если не передан, то если скрипт выполняется в браузере,
	 * берется имя испоняемого файла из переменной SERVER
	 *
	 * иначе берется имя текущего файла (т.е. config.log)
	 *********************************************************/
	function log($text = 'empty log line', $log = __FILE__)
	{
		if (!is_dir('./log')) mkdir('./log');

		if (isset($_SERVER)) $log = './log/' . pathinfo($_SERVER['URL'], PATHINFO_FILENAME) . '.log';
		else $log = './log/' . pathinfo($log, PATHINFO_FILENAME) . '.log';
		file_put_contents($log, date('[d F Y H:i:s] ') . $text . "\n", FILE_APPEND);
	}

	/*******************************************
	 * вывод SQL ошибок в лог
	 *
	 * используется только для вывода SQL ошибок
	 * функцией query
	 *******************************************/
	function log_errors($errors)
	{
		if (!is_dir('./log')) mkdir('./log');
		$log = './log/' . pathinfo($_SERVER['URL'] ?? $_SERVER['SCRIPT_NAME'], PATHINFO_FILENAME) . '.log';
		foreach ($errors as $error) {
			file_put_contents($log, date('[d F Y H:i:s]'), FILE_APPEND);
			file_put_contents($log, '[sqlstate: ' . $error['SQLSTATE'] . ']', FILE_APPEND);
			file_put_contents($log, '[code: ' . $error['code'] . '] ', FILE_APPEND);
			file_put_contents($log, $error['message'] . "\n", FILE_APPEND);
		}
	}

	/********************************
	 * генерация пароля
	 *******************************/
	function generate_pwd($length = 10)
	{
		$chars = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$numChars = strlen($chars);
		$string = '';
		for ($i = 0; $i < $length; $i++) {
			$string .= substr($chars, rand(1, $numChars) - 1, 1);
		}
		return $string;
	}
}
