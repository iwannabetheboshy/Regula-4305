<?php
if (!isset($_SESSION))
	session_start();
	//$_SESSION['SessionId']= session_id();
/*Проверка авторизации*/
if (!( (isset( $_SESSION['isAdmin'])) && ($_SESSION['isAdmin'] == 1) )) {
  header("Location: ../auth.php ");
  die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Панель Администратора</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <script src="../../js/jquery.min.js"></script>
  <script src="../../js/jquery-ui/external/jquery/jquery.js"></script>
  <script src="../../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
  
  <script>
  $(document).ready(function() 
  {

    function update()
    {
      $.ajax({
          type: "POST",
          url: "actions.php?type=0",
          success: function(json) 
          {    
              var data = $.parseJSON(json);
              var list = $(".table");
              list.find('tbody tr').remove();

              $.each(data, function(index, object) 
              {
                  var check = "";
                  if (object.TestAvailable) check = "checked=''";

                  var check1 = "";
                  if (object.UnsortQuestions) check1 = "checked=''";

                  var trainer = object.TrainerName;
                  if (trainer == null)
                    trainer = 'Все тренажёры';

                  var tr = $('<tr class="admItem" style="cursor:pointer">' + '<td>' + object.TestName + '</td><td class="col-md-4">' + object.TestDescription + '</td><td>' + 
                      trainer + '</td><td>' + object.MaxMinutesDuration + ' мин. </td><td>' + object.TestSessionCount + '</td><td>' +
                    '<div class="checkbox"><label><input type="checkbox" disabled ' + check1 + '></label></div></td>' +
                    '<td>' + '<div class="checkbox"><label><input type="checkbox" disabled ' + check + '></label></div></td><td>' +
                    '<button type="button" data-toggle="modal" data-target="#edit" class="glyphicon glyphicon-pencil btn-link"></button>' + '</td></tr>');
                  list.append(tr);

                  tr.on('click', function(e) 
                  {
                      $(location).attr('href', "questions?test=" + object.IdTest);          
                  });

                  tr.find(".glyphicon-pencil").first().on('click', function() 
                  {  
                    $("input[name='editIdTest']").val(object.IdTest);
                    $("input[name='editTestName']").val(object.TestName);
                    $("textarea[name='editTestDescription']").val(object.TestDescription);
                    $("input[name='editMaxMinutesDuration']").val(object.MaxMinutesDuration);
                    $("input[name='editIdTrainer']").val(object.IdTrainer);

                    $("input[name='editTestAvailable']").prop('checked', false);
                    if (object.TestAvailable)
                    $("input[name='editTestAvailable']").prop('checked', true);

                    $("input[name='editUnsortQuestions']").prop('checked', false);
                    if (object.UnsortQuestions)
                      $("input[name='editUnsortQuestions']").prop('checked', true);

                      $.ajax({
                        type: "POST",
                        url: "../select_all.php",
                        success: function(json) 
                        {    
                            var data = $.parseJSON(json);
                            var list = $(".dropdown-menu");
                            list.children().remove();

                            var selected = object.IdTrainer;

                            var tr = $('<li role="presentation"><a role="menuitem" tabindex="-1' + '" href="#">Все тренажёры</a></li>');
                            list.append(tr);

                            $.each(data, function(index, object) {
                                var tr = $('<li role="presentation"><a role="menuitem" tabindex="' + object.IdTrainer +'" href="#">' + object.Name + '</a></li>');
                                list.append(tr);
                                //Востанавливаем dropdown
                                if (selected == null)
                                {
                                  $("input[name='editIdTrainer']").val('NULL');
                                  $(".dropdown-toggle:first-child").text('Все тренажёры');
                                  $(".dropdown-toggle:first-child").val('Все тренажёры');
                                } else
                                if (selected == object.IdTrainer)
                                {
                                  $('#menu').data("tag", object.IdTrainer);
                                  $("input[name='editIdTrainer']").val(object.IdTrainer);
                                  $(".dropdown-toggle:first-child").text(object.Name);
                                  $(".dropdown-toggle:first-child").val(object.Name);
                                }
                            });

                            //Выделяем элементы в dropdown
                            $(".dropdown-menu li a").click(function()
                            {
                              $('#menu').data("tag", $(this).context.tabIndex);
                              
                              if ($(this).context.tabIndex == -1)
                                $("input[name='editIdTrainer']").val('NULL');
                              else
                                $("input[name='editIdTrainer']").val($(this).context.tabIndex);

                              $(".dropdown-toggle:first-child").text($(this).text());
                              $(".dropdown-toggle:first-child").val($(this).text());
                           });

                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });


                    $('#edit').modal('show');
                    return false;
                 });

              });
          },
          cache: false,
          contentType: false,
          processData: false
      });
    }

    $('#editForm').on('submit', function(event) 
    {
        var form_data = new FormData($(this)[0]);
        $.ajax({
          type: "POST",
          data: form_data, //{ IdAdmin: IdAdmin, pwd: pwd },
          url: "actions.php?type=2",
          success: function(json) 
          {    
              $('#edit').modal('hide');
              $('#editForm')[0].reset();
              update();
              return false;
          },
          cache: false,
          contentType: false,
          processData: false
        });

        event.preventDefault(); 
    });
    
    $('#formAddAdmin').on('submit', function(e) 
    {                 
        var form_data = new FormData($(this)[0]);
        $.ajax({
              type: "POST",
              url: "actions.php?type=1",
              data: form_data,
              success: function(data) 
              {
                    $('#addAdmin').modal('hide');
                    update();
                    return false;
              },
              cache: false,
            contentType: false,
            processData: false
        });
            
        e.preventDefault();
    });

    update();
    $("#HeaderName").text('Тесты');
  });

  </script>

</head>
<body>

<?php require_once '..' . DIRECTORY_SEPARATOR . 'site_top.php' ?>

<div class="container">
  <ul class="breadcrumb">
    <li><a href="../">Домой</a></li>
    <li class="active">Тесты</li>
  </ul>

  <div class="row">
    <div class="col-md-1">
      <button type="button" data-toggle="modal" data-target="#addAdmin" class="btn btn-success">Добавить</button>
    </div>
  </div>
  <br/>

  <!-- Modal Add -->
  <div class="modal fade" id="addAdmin" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Добавить новый тест</h4>
        </div>
        <form id="formAddAdmin">
          <div class="modal-body">
            <input type="hidden" class="form-control" name="IdTrainer" value='<?php if (isset($_GET['id'])) echo ($_GET['id']); else echo 'NULL'; ?>'>
            <div class="form-group">
              <label>Название</label>
              <input type="text" class="form-control" name="TestName">
            </div>
             <div class="form-group">
              <label>Описание</label>
              <textarea type="text" class="form-control" name="TestDescription"></textarea>
            </div>
            <div class="form-group">
              <label>Длительность теста (мин.)</label>
              <input type="number" class="form-control" name="MaxMinutesDuration">
            </div>
            <div class="form-group"> 
              <div class="col-sm-4">
                <div class="checkbox">
                  <label><input type="checkbox" name="TestAvailable">Тест доступен</label>
                </div>
              </div>
              <div class="checkbox col-sm-8">   
                  <label><input type="checkbox" name="UnsortQuestions">Сортировать вопросы</label>
              </div> 
            </div>
            </br>
            </br>
          <div id="message" class="alert alert-danger" style="display: none;">
              <strong>Ошибка!</strong> Указаны не все данные.
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Добавить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
        </form>
      </div>
      
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal fade" id="edit" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Изменить тест</h4>
        </div>
        <form id="editForm">
        <div class="modal-body">
            <input type="hidden" class="form-control" name="editIdTest">
            <input type="hidden" class="form-control" name="editIdTrainer">
            <div class="form-group">
              <label>Название</label>
              <input type="text" class="form-control" name="editTestName">
            </div>
             <div class="form-group">
              <label>Описание</label>
              <textarea type="text" class="form-control" name="editTestDescription"></textarea>
            </div>

            <div class="form-group">
            <label>Тренажёр</label>
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="menu" data-toggle="dropdown">Выбор...<span class="caret"></span></button>
                  <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                  </ul>
              </div>
            </div>

            <div class="form-group">
              <label>Длительность теста (мин.)</label>
              <input type="number" class="form-control" name="editMaxMinutesDuration">
            </div>

            <div class="form-group"> 
              <div class="col-sm-4">
                <div class="checkbox">
                  <label><input type="checkbox" name="editTestAvailable">Тест доступен</label>
                </div>
              </div>
              <div class="checkbox col-sm-8">   
                  <label><input type="checkbox" name="editUnsortQuestions">Сортировать вопросы</label>
              </div> 
            </div>
            </br>
            </br>
            <div id="editmessage" class="alert alert-danger" style="display: none;">
                <strong>Ошибка!</strong> Указаны не все данные.
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Изменить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
        </form>
      </div>
      
    </div>
  </div>

  <table class="table table-hover">
    <thead>
      <tr>
        <th>Название</th>
        <th>Описание</th>
        <th>Тренажёр</th>
        <th>Длительность</th>
        <th>Запусков</th>
        <th>Сортировка</th>
        <th>Показать</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>

</body>
</html>