<?php
if (!isset($_SESSION))
  session_start();
$_SESSION['SessionId'] = session_id();
define("AccessToFile", 1);

/*Проверка авторизации*/
if (!((isset($_SESSION['isAdmin'])) && ($_SESSION['isAdmin'] == 1))) {
  header("Location: auth.php ");
  die();
}
//Эту проверку вставить во все php к которым нужен доступ 
if (!defined("AccessToFile")) {
  die("Access Deny");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Панель Администратора</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../js/jquery-ui/jquery-ui.css">
  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>

  <style>
    a:link {
      color: black;
    }

    a:visited {
      color: gray;
    }

    a:hover {
      color: #4169E1;
    }

    a:active {
      color: blue;
    }

    .logintext {
      display: inline-block;
      padding-right: 10px;
    }
  </style>

  <script>
    $(document).ready(function() {

      $(".alert").hide();
      var select = function(event) {

        if ($('#menu').data("tag") != undefined)
          this.setAttribute("href", $(this).context.className + "?id=" + $('#menu').data("tag"));
        else
        if ($(this).prop("class") == "admins" || $(this).prop("class") == "tests" || $(this).prop("class") == "test_results")
          this.setAttribute("href", $(this).context.className);
        else {
          event.preventDefault();
          $(".alert").fadeIn("slow");
        }
      };

      $('.test_results, .tests, .stud, .images, .slaid, .admins, .saves').on('click', select);

      $('.logout').on('click', function() {
        sessionStorage.clear();
      });

      $.ajax({
        type: "POST",
        url: "select_all.php",
        success: function(json) {
          var data = $.parseJSON(json);
          var list = $(".dropdown-menu");

          $.each(data, function(index, object) {
            var tr = $('<li role="presentation"><a role="menuitem" tabindex="' + object.IdTrainer + '" href="#">' + object.Name + '</a></li>');
            list.append(tr);
            //Востанавливаем dropdown
            if (sessionStorage.tabIndex) {
              if (object.IdTrainer == sessionStorage.tabIndex) {
                $('#menu').data("tag", object.IdTrainer);
                $(".dropdown-toggle:first-child").text(object.Name);
                $(".dropdown-toggle:first-child").val(object.Name);
              }
            }
          });

          //Выделяем элементы в dropdown
          $(".dropdown-menu li a").click(function() {
            //Сохраняем dropdown
            if (typeof(Storage) !== "undefined") {
              sessionStorage.tabIndex = $(this).context.tabIndex;
              sessionStorage.trainerName = $(this).text();
            }

            $(".alert").fadeOut("slow");
            $('#menu').data("tag", $(this).context.tabIndex);
            $(".dropdown-toggle:first-child").text($(this).text());
            $(".dropdown-toggle:first-child").val($(this).text());
          });

        },
        cache: false,
        contentType: false,
        processData: false
      });
    });
  </script>

</head>

<body>
  <?php require_once 'site_top.php' ?>
  <div class="container-fluid">
    <div class="row">

      <div class="col-md-offset-3 col-md-6 alert alert-danger">
        <strong>Внимание!</strong> Для начала работы, выберите тренажёр.
      </div>

      <div class="col-md-6 col-md-offset-5">
        <h2>Тренажёр</h2>
        <p>Выберите тренажёр для начала работы с системой.</p>
        <div class="dropdown">
          <button class="btn btn-default dropdown-toggle" type="button" id="menu" data-toggle="dropdown">Выбор...<span class="caret"></span></button>
          <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
          </ul>
        </div>

      </div>
    </div>

    </br>
    </br>
    </br>
    <div class="row">
      <a href="#" class="stud">
        <div class="col-sm-offset-2 col-sm-2">
          <h3>Студенты</h3>
          <p>Просмотр сессий студентов, их действий на тренажёре и дополнительная информация</p>
        </div>
        </a>

        <a href="#" class="images">
          <div class="col-sm-2">
            <h3>Объекты контроля</h3>
            <p>Просмотр, добавление и редактирование объектов контроля для тренажёров</p>
          </div>
          </a>

          <a href="#" class="slaid">
            <div class="col-sm-2">
              <h3>Слайды</h3>
              <p>Просмотр, добавление, удаление и редактирование порядка слайдов...</p>
            </div>
            </a>

            <a href="#" class="admins">
              <div class="col-sm-2">
                <h3>Администраторы</h3>
                <p>Просмотр, добавление и удаление текущих администратов системы</p>
              </div>
              </a>
    </div>

    <div class="row">
      <a href="#" class="saves">
        <div class="col-sm-2 col-sm-offset-2">
          <h3>Изображения студентов</h3>
          <p>Просмотр и удаление изображений, загруженных студентами</p>
        </div>
        </a>

        <a href="#" class="tests">
          <div class="col-sm-2 ">
            <h3>Тесты</h3>
            <p>Просмотр, добавление и удаление существующих тестов в системе</p>
          </div>
        </a>

          <a href="#" class="test_results">
            <div class="col-sm-2 ">
              <h3>Результаты тестирования</h3>
              <p>Просмотр результатов тестирования студентов</p>
            </div>
          </a>

    </div>
  </div>

</body>

</html>