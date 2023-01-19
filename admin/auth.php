<?php
  if (!isset($_SESSION))
    session_start();
  $_SESSION['SessionId']= session_id();
  define("AccessToFile", 1);

    /*Вход*/  
    if ( (isset($_POST['Login'])) && (isset($_POST['Password'])) )  {
      require_once 'settings.php';
      
      $Login = $_POST['Login'];
      $Pass = $_POST['Password'];
      $SQL = "exec sp_check_AdminUser @Login = ? , @Password = ?";
      $result = $config->QueryWithParams($SQL, [$Login , $Pass]);

      if ((isset($result[0]['IdAdmin']))) {
        $_SESSION['IdAdmin'] = $result[0]['IdAdmin'];
        $_SESSION['UserName'] = $result[0]['UserName'];
        $_SESSION['Password'] = $Pass;
        $_SESSION['isAdmin'] = 1;
        //header("Location: index.php ");
      } else  {
        echo('<script>  $(document).ready(function() { $("#message").fadeIn(300).delay(2500).fadeOut(300); }); </script>');
        if (isset($_SESSION['SystemSessionId'])) {
          /*Студент пытается подобрать пароль к интерфейсу администратора*/
          $SQL = "exec sp_ins_StudentActivitiHist @IdSession = ? , @IdAction = ? ";
          if ($_SESSION['IdTrainer'] == 3)
          {
            $params = array($_SESSION['SystemSessionId'], 118 );
          }
          else if ($_SESSION['IdTrainer'] == 4) 
          {
            $params = array($_SESSION['SystemSessionId'], 119 );
          }
          else
            $params = array($_SESSION['SystemSessionId'], 57 );
          $config->QueryWithParams($SQL,$params);
        }
      }
  }

  /*И выход*/
  if ( (isset($_POST['Logout'])) || (isset($_GET['Logout'])) || (isset($_POST['logout'])) || (isset($_GET['logout'])))  {
    foreach ($_SESSION as $key => $value) {
      unset($value);
      unset($_SESSION[$key]);
    }
    session_unset();
  }

  /*Если пользователь уже вошел, то переадресуем его на основной сайт*/
  if ( (isset( $_SESSION['isAdmin'])) && ($_SESSION['isAdmin'] == 1) ) {
    header("Location: /admin ");
  }

?>

<html>
  <head>
      <title>VFRTA</title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">
      <link rel="stylesheet" href="../js/jquery-ui/jquery-ui.css">

      <style>
        .step1 {
          text-align: center;
        }       
        .alert {
          display: none;
        }      
      </style>

      <script src="../js/jquery.min.js"></script>
      <script src="../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
      <script src="../js/jquery-ui/jquery-ui.js"></script>
      <script src="../js/jquery.js"></script>
  </head>

  <body>      

  <div class="jumbotron text-center">
    <h1>Вход в панель администратора</h1>
    <p>Российская таможенная академия</p>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 step1">
        <h2></h2>
        <p>Для начала работы введите логин и пароль</p>
        <div id="message" class="alert alert-danger">
          <strong>Ошибка!</strong> Не верная пара логин-пароль.
        </div>
        <form id="sendStud" action="auth.php" method="post">
          <div class="form-group">
            <input type="text" class="form-control" name="Login" placeholder="Логин">
          </div>
          <div class="form-group">
            <input type="password" class="form-control" name="Password" placeholder="Пароль">
          </div>
          <button id="submit" type="submit" class="btn btn-primary active">Продолжить</button>
        </form>
          
      </div>
    </div>
  </body>
</html>